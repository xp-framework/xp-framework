<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.tds.TdsDataStream', 'rdbms.tds.TdsProtocolException', 'util.Date');

  /**
   * TDS protocol implementation
   *
   * @see   http://en.wikipedia.org/wiki/Tabular_Data_Stream
   * @see   http://msdn.microsoft.com/en-us/library/cc448435.aspx
   * @see   http://www.freetds.org/tds.html
   * @see   https://github.com/mono/mono/tree/master/mcs/class/Mono.Data.Tds/Mono.Data.Tds.Protocol
   */
  abstract class TdsProtocol extends Object {
    protected $stream= NULL;
    protected $done= FALSE;
    public $connected= FALSE;

    // Messages
    const MSG_QUERY     = 0x1;
    const MSG_LOGIN     = 0x2;
    const MSG_REPLY     = 0x4;
    const MSG_CANCEL    = 0x6;
    const MSG_LOGIN7    = 0x10;
    const MSG_LOGOFF    = 0x71;

    // Types
    const T_CHAR       = 0x2F;
    const T_VARCHAR    = 0x27;
    const T_INTN       = 0x26;
    const T_INT1       = 0x30;
    const T_DATE       = 0x31;
    const T_TIME       = 0x33;
    const T_INT2       = 0x34;
    const T_INT4       = 0x38;
    const T_INT8       = 0x7F;
    const T_FLT8       = 0x3E;
    const T_DATETIME   = 0x3D;
    const T_BIT        = 0x32;
    const T_TEXT       = 0x23;
    const T_NTEXT      = 0x63;
    const T_IMAGE      = 0x22;
    const T_MONEY4     = 0x7A;
    const T_MONEY      = 0x3C;
    const T_DATETIME4  = 0x3A;
    const T_REAL       = 0x3B;
    const T_BINARY     = 0x2D;
    const T_VOID       = 0x1F;
    const T_VARBINARY  = 0x25;
    const T_NVARCHAR   = 0x67;
    const T_BITN       = 0x68;
    const T_NUMERIC    = 0x6C;
    const T_DECIMAL    = 0x6A;
    const T_FLTN       = 0x6D;
    const T_MONEYN     = 0x6E;
    const T_DATETIMN   = 0x6F;
    const T_DATEN      = 0x7B;
    const T_TIMEN      = 0x93;
    const XT_CHAR      = 0xAF;
    const XT_VARCHAR   = 0xA7;
    const XT_NVARCHAR  = 0xE7;
    const XT_NCHAR     = 0xEF;
    const XT_VARBINARY = 0xA5;
    const XT_BINARY    = 0xAD;
    const T_UNITEXT    = 0xAE;
    const T_LONGBINARY = 0xE1;
    const T_SINT1      = 0x40;
    const T_UINT2      = 0x41;
    const T_UINT4      = 0x42;
    const T_UINT8      = 0x43;
    const T_UINTN      = 0x44;
    const T_UNIQUE     = 0x24;
    const T_VARIANT    = 0x62;
    const T_SINT8      = 0xBF;

    protected static $fixed= array(
      self::T_INT1     => 1,
      self::T_INT2     => 2,
      self::T_INT4     => 4,
      self::T_INT8     => 8,
      self::T_FLT8     => 8,
      self::T_BIT      => 1,
      self::T_MONEY4   => 4,
      self::T_MONEY    => 8,
      self::T_REAL     => 4,
      self::T_DATE     => 4,
      self::T_TIME     => 4,
      self::T_DATETIME => 8,
      self::T_SINT1    => 1,
      self::T_UINT2    => 2,
      self::T_UINT4    => 3,
      self::T_UINT8    => 8,
      self::T_SINT8    => 8,
    );

    /**
     * Creates a new protocol instance
     *
     * @param   peer.Socket s
     */
    public function __construct(Socket $s) {
      $this->stream= new TdsDataStream($s, $this->defaultPacketSize());
    }

    /**
     * Returns default packet size to use
     *
     * @return  int
     */
    protected abstract function defaultPacketSize();
    
    /**
     * Send login record
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    protected abstract function login($user, $password);

    /**
     * Connect
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    public function connect($user= '', $password= '') {
      $this->stream->connect();
      $this->login($user, $password);
      $response= $this->read();
      $this->cancel();    // TODO: Handle response, e.g. ENVCHANGE
      $this->connected= TRUE;
    }
    
    /**
     * Protocol read
     *
     * @return  string the message token
     * @throws  peer.ProtocolException
     */
    protected function read() {
      $this->done= FALSE;
      $type= $this->stream->begin();

      // Check for message type
      if (self::MSG_REPLY !== $type) {
        $this->cancel();
        throw new ProtocolException('Unknown message type '.$type);
      }
      
      // Handle errors - see also 2.2.5.7: Data Buffer Stream Tokens
      $token= $this->stream->getToken();
      if ("\xAA" === $token) {
        $meta= $this->stream->get('vlength/Vnumber/Cstate/Cclass', 8);
        $this->done= TRUE;
        throw new TdsProtocolException(
          $this->stream->getString($this->stream->getShort()),
          $meta['number'], 
          $meta['state'], 
          $meta['class'],
          $this->stream->getString($this->stream->getByte()),
          $this->stream->getString($this->stream->getByte()),
          $this->stream->getByte()
        );
      } else if ("\xE5" === $token) {
        $meta= $this->stream->get('vlength/Vnumber/Cstate/Cclass', 8);
        $this->stream->read($this->stream->getByte() + 1 + 2); // Skip SQLState, Status, TranState
        $this->done= TRUE;
        throw new TdsProtocolException(
          $this->stream->read($this->stream->getShort()),
          $meta['number'], 
          $meta['state'], 
          $meta['class'],
          $this->stream->read($this->stream->getByte()),
          $this->stream->read($this->stream->getByte()),
          $this->stream->getByte()
        );
      }
      
      return $token;
    }

    /**
     * Check whether connection is ready
     *
     * @return  bool
     */
    public function ready() {
      return $this->done;
    }

    /**
     * Cancel result set
     *
     */
    public function cancel() {
      if (!$this->done) {
        $this->stream->read(-1);    // TODO: Send cancel, then read rest. Will work like this, though, too.
        $this->done= TRUE;
      }
    }

    /**
     * Execute SQL in "fire and forget" mode.
     *
     * @param   string sql
     */
    public function exec($sql) {
      if (is_array($r= $this->query($sql))) {
        $this->cancel();
      }
    }

    /**
     * Issues a query and returns the results
     *
     * @param   string sql
     * @return  var
     */
    public abstract function query($sql);

    /**
     * Convert lo and hi values to money value
     *
     * @param   int lo
     * @param   int hi
     * @return  string
     */
    public function toMoney($lo, $hi) {
      if ($hi < 0) {
        $hi= ~$hi;
        $lo= ~($lo - 1);
        $div= -10000;
      } else {
        $div= 10000;
      }
      return bcdiv(bcadd(bcmul($hi, '4294967296'), $lo), $div, 5);
    }

    /**
     * Fetches one record
     *
     * @param   [:var][] fields
     * @return  [:var] record
     */
    public function fetch($fields) {
      $token= $this->stream->getToken();
      if ("\xAE" === $token) {    // TDS_CONTROL
        $length= $this->stream->getShort();
        for ($i= 0; $i < $length; $i++) {
          $this->stream->read($this->stream->getByte());
        }
      } else if ("\xD1" !== $token) {
        // DEBUG Console::$err->writeLinef('END TOKEN %02x', ord($token));
        $this->done= TRUE;
        return NULL;
      }
      
      $record= array();
      foreach ($fields as $i => $field) {
        switch ($field['type']) {
          case self::T_VARCHAR:
            $this->stream->read(1);   // Skip collation
            $len= $this->stream->getByte();
            $record[$i]= 0 === $len ? NULL : $this->stream->read($len);
            break;

          case self::XT_VARCHAR: case self::XT_NVARCHAR:
            $len= $this->stream->getShort();
            $record[$i]= 0xFFFF === $len ? NULL : $this->stream->read($len);
            break;

          case self::T_INTN:
            $len= $this->stream->getByte();
            switch ($len) {
              case 1: $record[$i]= $this->stream->getByte();
              case 2: $record[$i]= $this->stream->getShort();
              case 4: $record[$i]= $this->stream->getLong();
              default: $record[$i]= NULL;
            }
            break;

          case self::T_INT1:
            $record[$i]= $this->stream->getByte();
            break;

          case self::T_INT2:
            $record[$i]= $this->stream->getShort();
            break;

          case self::T_INT4:
            $record[$i]= $this->stream->getLong();
            break;

          case self::T_NUMERIC:
            $len= $this->stream->getByte()- 1;
            $pos= $this->stream->getByte();
            for ($j= 0, $n= 0, $m= $pos ? 1 : -1; $j < $len; $j+= 4, $m= bcmul($m, '4294967296')) {
              $n= bcadd($n, bcmul($this->stream->getLong(), $m));
            }
            if (0 === $field['scale']) {
              $record[$i]= $n;
            } else {
              $record[$i]= bcdiv($n, pow(10, $field['scale']), $field['prec']);
            }
            break;

          case self::T_FLTN:  // TODO: Convert to float
            $len= $this->stream->getByte();
            switch ($len) {
              case 4: $record[$i]= $this->stream->getLong(); break;
              case 8: $record[$i]= array($this->stream->getLong(), $this->stream->getLong()); break;
              default: $record[$i]= NULL;
            }
            break;

          case self::T_FLT8:  // TODO: Convert to float
            $record[$i]= array($this->stream->getLong(), $this->stream->getLong());
            break;

          case self::T_DATETIME:
            $days= $this->stream->getLong();
            $seconds= $this->stream->getLong();
            $record[$i]= Date::create(1900, 1, 1 + $days, 0, 0, $seconds / 300);
            break;

          case self::T_MONEYN:
            $len= $this->stream->getByte();
            switch ($len) {
              case 4: $record[$i]= $this->toMoney($this->stream->getLong()); break;
              case 8: $record[$i]= $this->toMoney($this->stream->getLong(), $this->stream->getLong()); break;
              default: $record[$i]= NULL;
            }
            break;

          case self::T_MONEY4:
            $record[$i]= $this->toMoney($this->stream->getLong());
            break;

          case self::T_MONEY:
            $record[$i]= $this->toMoney($this->stream->getLong(), $this->stream->getLong());
            break;

          default:
            Console::$err->writeLinef('Unknown field type 0x%02x', $field['type']);
        }
      }
      return $record;
    }

    /**
     * Close
     *
     */
    public function close() {
      if (!$this->connected) return;

      try {
        $this->stream->write(self::MSG_LOGOFF, "\0");
      } catch (IOException $ignored) {
        // Can't do much here
      } 

      $this->stream->close();
      $this->connected= FALSE;
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->stream).')';
    }
  }
?>
