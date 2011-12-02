<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.TdsProtocol');

  /**
   * TDS V7 protocol implementation
   *
   */
  class TdsV7Protocol extends TdsProtocol {

    static function __static() {
      parent::__static();
      self::$records[self::T_NUMERIC]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field) {
          $len= $stream->getByte()- 1;
          $pos= $stream->getByte();
          for ($j= 0, $n= 0, $m= $pos ? 1 : -1; $j < $len; $j+= 4, $m= bcmul($m, "4294967296")) {
            $n= bcadd($n, bcmul($stream->getLong(), $m));
          }
          if (0 === $field["scale"]) {
            return $n;
          } else {
            return bcdiv($n, pow(10, $field["scale"]), $field["prec"]);
          }
        }
      }');
    }

    /**
     * Scrambles password
     *
     * @param   string password
     * @return  string
     */
    protected function scramble($password) {
      $xor= 0x5A5A;
      $s= '';
      for ($i= 0, $l= strlen($password); $i < $l; $i++) {
        $c= ord($password{$i}) ^ $xor;
        $s.= chr((($c >> 4) & 0x0F0F) | (($c << 4) & 0xF0F0))."\xA5";
      }
      return $s;
    }

    /**
     * Returns default packet size to use
     *
     * @return  int
     */
    protected function defaultPacketSize() {
      return 4096;
    }

    /**
     * Connect
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    protected function login($user, $password) {
      $params= array(
        'hostname'   => array(TRUE, 'localhost'),
        'username'   => array(TRUE, $user),
        'password'   => array(FALSE, $this->scramble($password), strlen($password)),
        'appname'    => array(TRUE, 'XP-Framework'),
        'servername' => array(TRUE, 'localhost'),
        'unused'     => array(FALSE, '', 0),
        'library'    => array(TRUE, $this->getClassName()),
        'language'   => array(TRUE, ''),
        'database'   => array(TRUE, 'master')
      );
      
      // Initial packet
      $login= pack(
        'VVVVVCCCCVV',
        0x71000001,         //  4: TDSVersion 7.1
        0,                  //  8: PacketSize
        7,                  // 12: ClientProgVer
        getmypid(),         // 16: ClientPID
        0,                  // 20: ConnectionID
        0x20 | 0x40 | 0x80, // 24: OptionFlags1 (use warning, initial db change, set language)
        0x03,               // 25: OptionFlags2
        0,                  // 26: TypeFlags
        0,                  // 27: (FRESERVEDBYTE / OptionFlags3)
        0,                  // 28: ClientTimZone
        0                   // 32: ClientLCID
      );

      // Offsets
      $offset= 86;
      $data= '';
      foreach ($params as $name => $param) {
        if ($param[0]) {
          $chunk= iconv('iso-8859-1', 'ucs-2le', $param[1]);
          $length= strlen($param[1]);
        } else {
          $chunk= $param[1];
          $length= $param[2];
        }
        $login.= pack('vv', $offset, $length);
        $offset+= strlen($chunk);
        $data.= $chunk;
      }
      
      // Packet end
      $login.= "\x00\x00\x00\x00\x00\x00";  // ClientID
      $login.= pack(
        'vvvv',
        $offset, 0,         // SSPI
        $offset, 0          // SSPILong
      );

      // Login
      $this->stream->write(self::MSG_LOGIN7, pack('V', $offset).$login.$data);
    }
    

    /**
     * Issues a query and returns the results
     *
     * @param   string sql
     * @return  var
     */
    public function query($sql) {
      $this->stream->write(self::MSG_QUERY, iconv('iso-8859-1', 'ucs-2le', $sql));
      $token= $this->read();

      if ("\x81" === $token) {          // COLMETADATA
        $fields= array();
        $nfields= $this->stream->getShort();
        for ($i= 0; $i < $nfields; $i++) {
          $field= $this->stream->get('Cx1/Cx2/Cflags/Cx3/Ctype', 5);

          // Handle column. TODO: blob types - read table name
          if (self::T_NUMERIC === $field['type'] || self::T_DECIMAL === $field['type']) {
            $field['size']= $this->stream->getByte();
            $field['prec']= $this->stream->getByte();
            $field['scale']= $this->stream->getByte();
          } else if ($field['type'] > 128) {
            $field['size']= $this->stream->getShort();
            $this->stream->read(5);     // XXX Collation?
          } else if (isset(self::$fixed[$field['type']])) {
            $field['size']= self::$fixed[$field['type']];
          } else {
            $field['size']= $this->stream->getByte();
          }

          $field['name']= $this->stream->getString($this->stream->getByte());
          $fields[]= $field;
        }
        return $fields;
      } else if ("\xFD" === $token) {   // DONE
        $meta= $this->stream->get('vstatus/vcmd/Vrowcount', 8);
        $this->done= TRUE;
        // TODO: Maybe?
        return $meta['rowcount'];
      } else if ("\xE3" === $token) {   // ENVCHANGE, e.g. from "use [db]" queries
        // HANDLE!
        $this->cancel();
      } else {
        throw new TdsProtocolException(
          sprintf('Unexpected token 0x%02X', ord($token)),
          0,    // Number
          0,    // State
          0,    // Class
          NULL, // Server
          NULL, // Proc
          -1    // Line
        );
      }
    }
  }
?>
