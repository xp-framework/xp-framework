<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.tds.TdsDataStream', 'rdbms.tds.TdsRecord', 'rdbms.tds.TdsProtocolException');

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
    protected $records= array();
    public $connected= FALSE;

    // Record handler cache per base class implementation
    protected static $recordsFor= array();

    // Messages
    const MSG_QUERY    = 0x1;
    const MSG_LOGIN    = 0x2;
    const MSG_REPLY    = 0x4;
    const MSG_CANCEL   = 0x6;
    const MSG_LOGIN7   = 0x10;
    const MSG_LOGOFF   = 0x71;

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
      self::T_INT1      => 1,
      self::T_INT2      => 2,
      self::T_INT4      => 4,
      self::T_INT8      => 8,
      self::T_FLT8      => 8,
      self::T_BIT       => 1,
      self::T_MONEY4    => 4,
      self::T_MONEY     => 8,
      self::T_REAL      => 4,
      self::T_DATE      => 4,
      self::T_TIME      => 4,
      self::T_DATETIME4 => 4,
      self::T_DATETIME  => 8,
      self::T_SINT1     => 1,
      self::T_UINT2     => 2,
      self::T_UINT4     => 3,
      self::T_UINT8     => 8,
      self::T_SINT8     => 8,
    );

    static function __static() {
      self::$recordsFor[0][self::T_VARCHAR]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= $stream->getByte();
          return 0 === $len ? NULL : $stream->read($len);
        }
      }');
      self::$recordsFor[0][self::XT_VARCHAR]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= $stream->getShort();
          return 0xFFFF === $len ? NULL : $stream->read($len);
        }
      }');
      self::$recordsFor[0][self::XT_NVARCHAR]= self::$recordsFor[0][self::XT_VARCHAR];
      self::$recordsFor[0][self::T_INTN]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= isset($field["len"]) ? $field["len"] : $stream->getByte();
          switch ($len) {
            case 1: return $stream->getByte();
            case 2: return $stream->getShort();
            case 4: return $stream->getLong();
            case 8: return $this->toNumber($stream->getInt64(), 0, 0);
            default: return NULL;
          }
        }
      }');
      self::$recordsFor[0][self::T_INT1]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $stream->getByte();
        }
      }');
      self::$recordsFor[0][self::T_INT2]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $stream->getShort();
        }
      }');
      self::$recordsFor[0][self::T_INT4]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $stream->getLong();
        }
      }');
      self::$recordsFor[0][self::T_INT8]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toNumber($stream->getInt64(), 0, 0);
        }
      }');
      self::$recordsFor[0][self::T_FLTN]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= isset($field["len"]) ? $field["len"] : $stream->getByte();
          switch ($len) {
            case 4: return $this->toFloat($stream->read(4)); break;
            case 8: return $this->toDouble($stream->read(8)); break;
            default: return NULL;
          }
        }
      }');
      self::$recordsFor[0][self::T_FLT8]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toDouble($stream->read(8));
        }
      }');
      self::$recordsFor[0][self::T_REAL]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toFloat($stream->read(4));
        }
      }');
      self::$recordsFor[0][self::T_DATETIME]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toDate($stream->getLong(), $stream->getLong());
        }
      }');
      self::$recordsFor[0][self::T_DATETIME4]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toDate($stream->getShort(), $stream->getShort() * 60);
        }
      }');
      self::$recordsFor[0][self::T_DATETIMN]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= isset($field["len"]) ? $field["len"] : $stream->getByte();
          switch ($len) {
            case 4: return $this->toDate($stream->getShort(), $stream->getShort() * 60); break;
            case 8: return $this->toDate($stream->getLong(), $stream->getLong()); break;
            default: return NULL;
          }
        }
      }');
      self::$recordsFor[0][self::T_MONEYN]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $len= isset($field["len"]) ? $field["len"] : $stream->getByte();
          switch ($len) {
            case 4: return $this->toMoney($stream->getLong()); break;
            case 8: return $this->toMoney($stream->getLong(), $stream->getLong()); break;
            default: return NULL;
          }
        }
      }');
      self::$recordsFor[0][self::T_MONEY4]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toMoney($stream->getLong());
        }
      }');
      self::$recordsFor[0][self::T_MONEY]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $this->toMoney($stream->getLong(), $stream->getLong());
        }
      }');
      self::$recordsFor[0][self::T_CHAR]= self::$recordsFor[0][self::T_VARCHAR];
      self::$recordsFor[0][self::XT_CHAR]= self::$recordsFor[0][self::XT_VARCHAR];
      self::$recordsFor[0][self::T_TEXT]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          $has= $stream->getByte();
          if ($has !== 16) return NULL;

          $stream->read(24);  // Skip 16 Byte TEXTPTR, 8 Byte TIMESTAMP

          $len= $stream->getLong();
          if ($len === 0) return $field["status"] & 0x20 ? NULL : "";

          return $stream->read($len);
        }
      }');
      self::$recordsFor[0][self::T_NTEXT]= self::$recordsFor[0][self::T_TEXT];
      self::$recordsFor[0][self::T_BITN]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $stream->getByte() ? $stream->getByte() : NULL;
        }
      }');
      self::$recordsFor[0][self::T_BIT]= newinstance('rdbms.tds.TdsRecord', array(), '{
        public function unmarshal($stream, $field, $records) {
          return $stream->getByte();
        }
      }');
    }

    /**
     * Creates a new protocol instance
     *
     * @param   peer.Socket s
     */
    public function __construct(Socket $s) {
      $this->stream= new TdsDataStream($s, $this->defaultPacketSize());

      // Cache record handlers per instance
      $impl= $this->getClassName();
      if (!isset(self::$recordsFor[$impl])) {
        self::$recordsFor[$impl]= $this->setupRecords() + self::$recordsFor[0];
      }
      $this->records= self::$recordsFor[$impl];
    }

    /**
     * Setup record handlers
     *
     * @return  [:rdbms.tds.TdsRecord] handlers
     */
    protected abstract function setupRecords();

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
     * Handles ERROR messages (0xAA)
     *
     * @throws  rdbms.tds.TdsProtocolException
     */
    protected function handleError() {
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
    }

    /**
     * Handles INFO messages (0xAB)
     *
     * @throws  rdbms.tds.TdsProtocolException
     */
    protected function handleInfo() {
      $meta= $this->stream->get('vlength/Vnumber/Cstate/Cclass', 8);
      $message= $this->stream->getString($this->stream->getShort());
      $server= $this->stream->getString($this->stream->getByte());
      $proc= $this->stream->getString($this->stream->getByte());
      $line= $this->stream->getShort();

      // TODO message handling
      // DEBUG Console::$err->writeLine($server, ': ', $message, ' in ', $proc, ' line ', $line);
    }

    /**
     * Handles EED text messages (0xE5)
     *
     * @throws  rdbms.tds.TdsProtocolException
     */
    protected function handleExtendedError() {
      $meta= $this->stream->get('vlength/Vnumber/Cstate/Cclass', 8);
      $meta['sqlstate']= $this->stream->read($this->stream->getByte());
      $meta= array_merge($meta, $this->stream->get('Cstatus/vtranstate', 3));
      $message= $this->stream->read($this->stream->getShort());
      $server= $this->stream->read($this->stream->getByte());
      $proc= $this->stream->read($this->stream->getByte());
      $line= $this->stream->getShort();
      $this->done= TRUE;

      // Fetch TDS_DONE (FD, FE, FF) associated with this EED and check for the error bit
      $done= $this->stream->get('Ctoken/vstatus/vcmd/Vrowcount', 9);
      if ($done['token'] < 0xFD || $done['status'] & 0x0002) {
        throw new TdsProtocolException(
          $message,
          $meta['number'],
          $meta['state'],
          $meta['class'],
          $server,
          $proc,
          $line
        );
      }

      // TODO message handling
      // Console::$err->writeLine($server, ': ', $message, ' in ', $proc, ' line ', $line);
    }

    /**
     * Handle ENVCHANGE
     *
     * @param  int type
     * @param  string old
     * @param  string new
     * @param  bool initial if this ENVCHANGE was part of the login response
     */
    protected function handleEnvChange($type, $old, $new, $initial= FALSE) {
      // Intentionally empty
    }

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

      if ("\xAD" === $response) {          // TDS_LOGINACK
        $meta= $this->stream->get('vlength/Cstatus', 3);
        switch ($meta['status']) {
          case 5:     // TDS_LOG_SUCCEED
            $this->stream->read($meta['length']- 1);
            break;

          case 6:     // TDS_LOG_FAIL
            $this->stream->read($meta['length']- 1);
            $this->stream->getToken();    // 0xE5
            $this->handleExtendedError();
            break;

          case 7:     // TDS_LOG_NEGOTIATE
            $this->stream->read($meta['length']- 1);
            throw new TdsProtocolException('Negotiation not yet implemented');
        }
      } else if ("\xE3" === $response) {   // TDS_ENVCHANGE
        $this->envchange();
      } else {
        $this->cancel();                   // TODO: What else could we get here?
      }

      $this->connected= TRUE;
    }

    /**
     * Process an ENVCHANGE token, e.g. "\015\003\005iso_1\005cp850"
     *
     */
    protected function envchange() {
      $len= $this->stream->getShort();
      $env= $this->stream->read($len);
      $i= 0;
      while ($i < $len) {
        $type= $env{$i++};
        $new= substr($env, $i+ 1, $l= ord($env{$i++}));
        $i+= $l;
        $old= substr($env, $i+ 1, $l= ord($env{$i++}));
        $i+= $l;
        $this->handleEnvChange(ord($type), $old, $new, TRUE);
      }
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
        $this->handleError();
        // Raises an exception
      } else if ("\xE5" === $token) {
        $this->handleExtendedError();
        $token= $this->stream->getToken();
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
     * Fetches one record
     *
     * @param   [:var][] fields
     * @return  [:var] record
     */
    public function fetch($fields) {
      $token= $this->stream->getToken();
      do {
        if ("\xAE" === $token) {              // TDS_CONTROL
          $length= $this->stream->getShort();
          for ($i= 0; $i < $length; $i++) {
            $this->stream->read($this->stream->getByte());
          }
          $token= $this->stream->getToken();
          $continue= TRUE;
        } else if ("\xAA" === $token) {
          $this->handleError();
          // Always raises an exception
        } else if ("\xE5" === $token) {
          $this->handleExtendedError();
          $token= $this->stream->getToken();
          $continue= TRUE;
        } else if ("\xA9" === $token) {       // TDS_COLUMNORDER
          $this->stream->read($this->stream->getShort());
          $token= $this->stream->getToken();
          $continue= TRUE;
        } else if ("\xFE" === $token || "\xFF" === $token) {
          $meta= $this->stream->get('vstatus/vcmd/Vrowcount', 8);
          if ($meta['status'] & 0x0001) {
            $token= $this->stream->getToken();
            $continue= TRUE;
          } else {
            $this->done= TRUE;
            return NULL;
          }
        } else if ("\xD1" !== $token) {
          // Console::$err->writeLinef('END TOKEN %02x', ord($token));    // 2.2.5.7 Data Buffer Stream Tokens
          $this->done= TRUE;
          return NULL;
        } else {
          $continue= FALSE;
        }
      } while ($continue);
      
      $record= array();
      foreach ($fields as $i => $field) {
        $type= $field['type'];
        if (!isset($this->records[$type])) {
          Console::$err->writeLinef('Unknown field type 0x%02x', $type);
          continue;
        }

        $record[$i]= $this->records[$type]->unmarshal($this->stream, $field, $this->records);
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
