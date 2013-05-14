<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.mysqlx.MySqlxProtocolException', 'rdbms.mysqlx.MySqlPassword', 'util.Date');

  /**
   * MySQL protocol implementation
   *
   * @see   http://forge.mysql.com/wiki/MySQL_Internals_ClientServer_Protocol
   */
  class MySqlxProtocol extends Object {
    protected $pkt= 0;
    protected $sock= NULL;
    public $connected= FALSE;
    const MAX_PACKET_LENGTH = 16777215;   // 256 * 256 * 256 - 1

    // Client flags
    const CLIENT_LONG_PASSWORD     = 0x00001;  
    const CLIENT_FOUND_ROWS        = 0x00002;  
    const CLIENT_LONG_FLAG         = 0x00004;  
    const CLIENT_CONNECT_WITH_DB   = 0x00008;  
    const CLIENT_NO_SCHEMA         = 0x00010;  
    const CLIENT_COMPRESS          = 0x00020;  
    const CLIENT_ODBC              = 0x00040;  
    const CLIENT_LOCAL_FILES       = 0x00080;  
    const CLIENT_IGNORE_SPACE      = 0x00100;  
    const CLIENT_PROTOCOL_41       = 0x00200;  
    const CLIENT_INTERACTIVE       = 0x00400;  
    const CLIENT_SSL               = 0x00800;  
    const CLIENT_IGNORE_SIGPIPE    = 0x01000;  
    const CLIENT_TRANSACTIONS      = 0x02000; 
    const CLIENT_RESERVED          = 0x04000;
    const CLIENT_SECURE_CONNECTION = 0x08000;
    const CLIENT_MULTI_STATEMENTS  = 0x10000;
    const CLIENT_MULTI_RESULTS     = 0x20000;

    // Commands
    const COM_SLEEP           = 0;
    const COM_QUIT            = 1;
    const COM_INIT_DB         = 2;
    const COM_QUERY           = 3;
    const COM_FIELD_LIST      = 4;
    const COM_CREATE_DB       = 5;
    const COM_DROP_DB         = 6;
    const COM_REFRESH         = 7;
    const COM_SHUTDOWN        = 8;
    const COM_STATISTICS      = 9;
    const COM_PROCESS_INFO    = 10;
    const COM_CONNECT         = 11;
    const COM_PROCESS_KILL    = 12;
    const COM_DEBUG           = 13;
    const COM_PING            = 14;
    const COM_TIME            = 15;
    const COM_DELAYED_INSERT  = 16;
    const COM_CHANGE_USER     = 17;
    const COM_BINLOG_DUMP     = 18;
    const COM_TABLE_DUMP      = 19;
    const COM_CONNECT_OUT     = 20;
    const COM_REGISTER_SLAVE  = 21;
    
    /**
     * Creates a new protocol instance
     *
     * @param   peer.Socket s
     */
    public function __construct(Socket $s) {
      $this->sock= $s;
    }

    /**
     * Connect
     *
     * @param   string user
     * @param   string user
     * @throws  io.IOException
     */
    public function connect($user= '', $password= '') {
      $this->sock->isConnected() || $this->sock->connect();
      $buf= $this->read();
      
      // Protocol and server version
      $proto= ord($buf[0]);
      $p= strpos($buf, "\0");
      $version= substr($buf, 1, $p- 1);
      if (10 !== $proto) {
        throw new ProtocolException('MySQL Protocol version #'.$proto.' not supported, server '.$version);
      }

      // Scramble
      $init= unpack('Lthread/a8scramble', substr($buf, $p+ 1, 13));
      if (strlen($buf)- $p- 14 >= 2) {
        $capabilities= current(unpack('v', substr($buf, $p+ 14, 2)));
      } else {
        $capabilities= 0;
      }
      if (strlen($buf)- $p- 16 >= 3) {
        $extra= unpack('clanguage/vstatus', substr($buf, $p+ 16, 3));
      } else {
        $extra= NULL;
      }
      if (strlen($buf)- $p- 32 >= 12) {
        $init['scramble'].= substr($buf, $p+ 32, 12);
      }
      
      // Handshake depending on server protocol
      if ($capabilities & self::CLIENT_PROTOCOL_41) {
        $flags= (
          self::CLIENT_LONG_PASSWORD | 
          self::CLIENT_LONG_FLAG | 
          self::CLIENT_TRANSACTIONS | 
          self::CLIENT_PROTOCOL_41 |
          self::CLIENT_SECURE_CONNECTION |
          self::CLIENT_MULTI_RESULTS
        );
        $data= (
          pack('V', $flags).
          pack('V', 1024 * 1024 * 1024).
          chr(8).                    // Charset 8 = latin1, 33 = utf8
          str_repeat("\0", 23).      // Filler
          $user."\0".
          ($password ? chr(20).MySqlPassword::$PROTOCOL_41->scramble($password, $init['scramble']) : chr(0))
        );
        $this->fieldparser= 'field_41';
      } else {
        $flags= (
          self::CLIENT_LONG_PASSWORD | 
          self::CLIENT_LONG_FLAG | 
          self::CLIENT_TRANSACTIONS | 
          self::CLIENT_MULTI_RESULTS
        );
        $data= (
          pack('v', $flags).
          $this->int3(1024 * 1024 * 1024).
          $user."\0".
          substr(MySqlPassword::$PROTOCOL_40->scramble($password, $init['scramble']), 0, 8)."\0"
        );
        $this->fieldparser= 'field_40';
      }
      $this->write($data);
      $answer= $this->read();
      
      // By sending this very specific reply server asks us to send scrambled password in old format. 
      if (1 === strlen($answer) && "\376" === $answer[0] && $capabilities & self::CLIENT_SECURE_CONNECTION) {
        $this->write(substr(MySqlPassword::$PROTOCOL_40->scramble($password, substr($init['scramble'], 0, -12)), 0, 8)."\0");
        $answer= $this->read();
      }
      
      $this->pkt= 0;
      $this->connected= TRUE;
    }

    /**
     * Close
     *
     */
    public function close() {
      if (!$this->sock->isConnected()) return;

      try {
        $this->write(chr(self::COM_QUIT));
      } catch (IOException $ignored) {
        // Can't do much here
      }
      $this->sock->close();
      $this->connected= FALSE;
      $this->pkt= 0;
    }
    
    /**
     * Returns an integer serialized as INT3
     *
     * @param   int n
     * @return  string
     */
    protected function int3($n) {
      return pack('cv', $n % 256, $n >> 8);
    }
    
    /**
     * Returns length encoded in first character(s) of data
     *
     * @param   string data
     * @param   &int consumed
     * @param   bool ll default FALSE
     * @return  int
     */
    protected function length($data, &$consumed, $ll= FALSE) {
      $o= $consumed;

      // Prevent against E_WARNINGs when offet exceeds lengths in code
      // below.
      if (!isset($data[$consumed])) {
        return 0;
      }
      switch ($data{$consumed}) {
        case "\373": $consumed+= 1; return NULL;
        case "\374": $consumed+= 3; return ord($data[$o+ 1]) + ord($data[$o+ 2]) * 256;
        case "\375": $consumed+= 4; return ord($data[$o+ 1]) + ord($data[$o+ 2]) * 256 + ord($data[$o+ 3]) * 65536;
        case "\376": $consumed+= 9; return $ll
          ? $data[$o+ 1] + $data[$o+ 2] * 256 + $data[$o+ 3] * 65536 + $data[$o+ 4] * pow(256, 3) + $data[$o+ 5] * pow(256, 4) + $data[$o+ 6] * pow(256, 5) + $data[$o+ 6] * pow(256, 6) + $data[$o+ 8] * pow(256, 7)
          : $data[$o+ 1] + $data[$o+ 2] * 256 + $data[$o+ 3] * 65536 + $data[$o+ 4] * pow(256, 3)
        ;
        default: $consumed+= 1; return ord($data[$o]);
      }
    }
    
    /**
     * Returns length encoded binary
     *
     * @param   string data
     * @param   int l
     * @param   &int consumed
     * @return  int
     */
    protected function lbin($data, $l, &$consumed) {
      $o= $consumed+ 1;
      switch ($l) {
        case 2: $n= ord($data[$o]); break;
        case 3: $n= ord($data[$o]) + ord($data[$o+ 1]) * 256;
        case 4: $n= ord($data[$o]) + ord($data[$o+ 1]) * 256 + ord($data[$o+ 2]) * 65536;
      }
      $consumed+= $l;
      return $n;
    }
    
    /**
     * Returns length encoded string
     *
     * @param   string data
     * @param   &int consumed
     * @return  string
     */
    protected function lstr($data, &$consumed) {
      if ("\373" === $data{$consumed}) {
        $consumed++;
        return NULL;
      }

      $l= $this->length($data, $consumed);
      $r= substr($data, $consumed, $l);
      $consumed+= $l;
      return $r;
    }
    
    /**
     * Field information (4.1+)
     *
     * @param   string
     * @return  [:var] meta
     */
    protected function field_41($f) {
      $consumed= 0;
      $field= array();
      $field['catalog']= $this->lstr($f, $consumed);
      $field['db']= $this->lstr($f, $consumed);
      $field['table']= $this->lstr($f, $consumed);
      $field['org_table']= $this->lstr($f, $consumed);
      $field['name']= $this->lstr($f, $consumed);
      $field['org_name']= $this->lstr($f, $consumed);
      return array_merge($field, unpack('vcharsetnr/Vlength/Ctype/vflags/Cdecimals', substr($f, $consumed+ 1, 10)));
    }

    /**
     * Field information (<=4.0)
     *
     * @param   string
     * @return  [:var] meta
     */
    protected function field_40($f) {
      $consumed= 0;
      $field= array();
      $field['catalog']= $field['db']= NULL;
      $field['table']= $this->lstr($f, $consumed);
      $field['org_table']= NULL;
      $field['name']= $this->lstr($f, $consumed);
      $field['org_name']= NULL;
      $field['length']= $this->lbin($f, 4, $consumed);
      $field['type']= $this->lbin($f, 2, $consumed);
      $field['flags']= $this->lbin($f, 2, $consumed);
      $field['decimals']= ord($f{$consumed+ 1});
      return $field;
    }
    
    /**
     * Issues a query and returns the results
     *
     * @param   string sql
     * @return  var
     */
    public function query($sql) {
      $this->write(chr(self::COM_QUERY).$sql);
      $data= $this->read();
      $consumed= 0;
      $nfields= $this->length($data, $consumed);
      
      if (NULL === $nfields) {
        $this->pkt= 0;
        throw new ProtocolException('LOAD DATA LOCAL INFILE not implemented');
      } else if (0 === $nfields) {      // Results from an insert / update / delete query
        $affected= $this->length($data, $consumed, TRUE);
        $identity= $this->length($data, $consumed, TRUE);
        $this->pkt= 0;
        return $affected;
      } else {                          // Result sets, process fields and EOF record
        $extra= $this->length($data, $consumed, TRUE);
        $fields= array();
        for ($i= 0; $i < $nfields; $i++) {
          $fields[]= $this->{$this->fieldparser}($this->read());
        }
        $this->read();                  // EOF[ields] record
        
        // DEBUG Console::$err->writeLine('F ', $fields);
        return $fields;
      }
    }
    
    /**
     * Check whether connection is ready
     *
     * @return  bool
     */
    public function ready() {
      return 0 === $this->pkt;
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
      $this->pkt= 0;
    }
    
    /**
     * Fetches one record
     *
     * @param   [:var][] fields
     * @return  [:var] record
     */
    public function fetch($fields) {
      $r= $this->read();
      if ("\376" === $r{0} && strlen($r) < 9) {
        $this->pkt= 0;
        return NULL;
      }

      $record= array();
      $consumed= 0;
      foreach ($fields as $i => $field) {
        $value= $this->lstr($r, $consumed);
        $record[$i]= $value;
      }
      return $record;
    }
    
    /**
     * Cancel result set
     *
     * @return  int rows read
     */
    public function cancel() {
      $i= 0;
      do {
        $r= $this->read();
        $i++;
      } while (!("\376" === $r{0} && strlen($r) < 9));
      $this->pkt= 0;
      return $i;
    }
    
    /**
     * Consume entire result set
     *
     * @param   [:var][] fields
     * @return  [:var][] records
     */
    public function consume($fields) {
      $records= array();
      while ($record= $this->fetch($fields)) {
        $records[]= $record;
      }
      $this->pkt= 0;
      return $records;
    }
    
    /**
     * Protocol write
     *
     * @param   string arg
     * @throws  peer.ProtocolException
     */
    protected function write($arg) {
      // DEBUG Console::$err->writeLine('W-> ', new Bytes($arg));

      $ptr= 0;
      while (strlen($arg)- $ptr > self::MAX_PACKET_LENGTH) {
        $this->sock->write($this->int3(self::MAX_PACKET_LENGTH).chr($this->pkt).substr($arg, $ptr, self::MAX_PACKET_LENGTH));
        $this->pkt= $this->pkt+ 1 & 0xFF;
        $ptr+= self::MAX_PACKET_LENGTH;
      }
      $this->sock->write($this->int3(strlen($arg)- $ptr).chr($this->pkt).substr($arg, $ptr));
      $this->pkt= $this->pkt+ 1 & 0xFF;
    }
    
    /**
     * Read a number of bytes
     *
     * @param   int bytes
     * @return  string
     */
    protected function readFully($bytes) {
      $b= '';
      while (($s= strlen($b)) < $bytes) {
        $b.= $this->sock->readBinary($bytes- $s);
      }
      return $b;
    }
    
    /**
     * Protocol read
     *
     * @return  string
     * @throws  peer.ProtocolException
     */
    protected function read() {
      $len= NULL;
      $buf= '';

      while (NULL === $len || self::MAX_PACKET_LENGTH === $len) {
        $a= $this->sock->readBinary(4);
        $len= ord($a[0]) + ord($a[1]) * 0x100 + ord($a[2]) * 0x10000;
        $pkt= ord($a[3]);
        if ($pkt != $this->pkt) {
          throw new ProtocolException('Packet no. out of order, have '.$pkt.', expecting '.$this->pkt);
        }
        $this->pkt= $this->pkt+ 1 & 0xFF;
        $buf.= $this->readFully($len);
      }

      // DEBUG Console::$err->writeLine('R-> ', new Bytes($buf));
      
      // 0xFF indicates an error
      if ("\377" !== $buf{0}) return $buf;

      $this->pkt= 0;
      $sqlstate= '00000';
      $errno= -1;
      $error= 'Unknown error';
      if (strlen($buf) > 3) {
        $errno= ord($buf[1])+ ord($buf[2]) * 0x100;
        if ('#' === $buf[$p= 3]) {
          $sqlstate= substr($buf, 4, 5);
          $p= 9;
        }
        $error= substr($buf, $p);
      }
      throw new MySqlxProtocolException($error, $errno, $sqlstate);
    }

    /**
     * Returns a hashcode for this protocol instance
     *
     * @return  string
     */
    public function hashCode() {
      return (string)(int)$this->sock->getHandle();
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->sock->getHandle()).', P@'.$this->pkt.')';
    }
  }
?>
