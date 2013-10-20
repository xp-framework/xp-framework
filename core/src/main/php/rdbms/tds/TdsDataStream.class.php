<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.TdsProtocolException');

  /**
   * A TDS data stream
   *
   * @see     xp://rdbms.tds.TdsV7Protocolo#read
   * @test    xp://net.xp_framework.unittest.rdbms.tds.TdsDataStreamTest
   */
  class TdsDataStream extends Object {
    protected $pkt= 0;
    protected $packetSize= 0;
    protected $sock= NULL;
    protected $buffer= '';
    protected $header= array('status' => 0, 'length' => -1);

    /**
     * Creates a new data stream on a given socket with a given packet size
     *
     * @param   peer.Socket sock
     * @param   int packetSize default 512
     */
    public function __construct($sock, $packetSize= 512) {
      if ($packetSize < 9) {
        throw new IllegalArgumentException('Packet size must be at least 9');
      }
      $this->sock= $sock;
      $this->packetSize= $packetSize;
    }

    /**
     * Helper method for dump()
     *
     * @param   string bytes
     * @param   int offset
     * @return  string
     */
    protected static function chars($bytes, $offset) {
      $s= '';
      for ($j= $offset- 16, $l= min($offset, strlen($bytes)); $j < $l; $j++) {
        $c= $bytes{$j};
        $s.= $c < "\x20" || $c > "\x7F" ? ' ' : $c;
      }
      return $s;
    }

    /**
     * Creates a hexdump
     *
     * @param   string bytes
     * @return  string
     */
    protected static function dump($bytes) {
      $n= strlen($bytes);
      if ($r= ($n % 16)) {
        $bytes.= str_repeat("\x00", 16 - $r);
      }
      
      $s= '';
      for ($i= 0; $i < $n; $i++) {
        if (0 === $i) {
          $s= '  0: ';
        } else if (0 === ($i % 16)) {
          $s.= sprintf("|%s|\n%3d: ", self::chars($bytes, $i), $i);
        }
        $s.= sprintf('%02X ', ord($bytes{$i}));
      }
      return $s;
    }
    
    /**
     * Connect
     *
     */
    public function connect() {
      $this->sock->isConnected() || $this->sock->connect();
    }
    
    /**
     * Close
     *
     */
    public function close() {
      $this->sock->isConnected() && $this->sock->close();
    }

    /**
     * Protocol write
     *
     * @param   int type the message type one of the MSG_* constants
     * @param   string arg
     * @throws  peer.ProtocolException
     */
    public function write($type, $arg) {
      $offset= $status= 0;
      do {
        $length= strlen($arg)+ 8 - $offset;
        if ($length > $this->packetSize) {
          $length= $this->packetSize;
          $status= 0;
        } else {
          $status= 1;
        }
        $chunk= substr($arg, $offset, $length- 8);
        $packet= pack('CCnnCc', $type, $status, $length, 0x0000, $this->pkt, 0).$chunk;

        // DEBUG Console::$err->writeLine("W->\n".self::dump($packet));

        $this->sock->write($packet);
        $offset+= $length- 8;
      } while (0 === $status);
      $this->pkt= $this->pkt+ 1 & 0xFF;
    }

    /**
     * Get 
     *
     * @param   string format
     * @param   int length
     * @return  string
     */
    public function get($format, $length) {
      return unpack($format, $this->read($length));
    }

    /**
     * Get a token (reads one byte)
     *
     * @return  string
     */
    public function getToken() {
      return $this->read(1);
    }

    /**
     * Get a byte
     *
     * @return  int
     */
    public function getByte() {
      $u= unpack('C', $this->read(1));
      return $u[1];
    }

    /**
     * Get a short (2 bytes)
     *
     * @return  int
     */
    public function getShort() {
      $u= unpack('v', $this->read(2));
      return $u[1];
    }

    /**
     * Get a long (4 bytes)
     *
     * @return  int
     */
    public function getLong() {
      $u= unpack('I', $this->read(4));
      return $u[1];
    }

    /**
     * Get a 64-bit integer (8 bytes)
     *
     * @return  string
     */
    public function getInt64() {
      $u= unpack('VV', $this->read(8));
      return bcadd($u[1], bcmul($u[2] + $u[1] < 0, "4294967296", 0), 0);
    }

    /**
     * Reads a string
     *
     * @param   int length
     * @return  string
     */
    public function getString($length) {
      if (0 === $length) return NULL;
      return iconv('ucs-2le', xp::ENCODING.'//IGNORE', $this->read($length * 2));
    }
    
    /**
     * Begin reading a message
     *
     * @return  int message type
     */
    public function begin() {
      $this->header= array('status' => 0, 'length' => -1);
      $this->buffer= '';
      $this->read0(1);
      return $this->header['type'];
    }

    /**
     * Read a specified number of bytes from a given stream
     *
     * @param   int length
     * @return  string
     */
    protected function readFully($length) {
      $return= '';
      while (strlen($return) < $length) {
        if (0 == strlen($buf= $this->sock->readBinary($length - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }

    /**
     * Check for buffer underrun and read as many packets as necessary
     *
     * @param   int length
     * @return  int maximum length available
     * @throws  rdbms.tds.TdsProtocolException
     */
    protected function read0($length) {
      while (-1 === $length || strlen($this->buffer) < $length) {
        if (1 === $this->header['status']) return strlen($this->buffer);

        $bytes= $this->readFully(8);
        $this->header= unpack('Ctype/Cstatus/nlength/nspid/Cpacket/cwindow', $bytes);
        if (FALSE === $this->header) {
          $this->header['status']= 1;
          $e= new TdsProtocolException('Expecting header, have unknown byte sequence '.addcslashes($bytes, "\0..\17\177..\377"));
          xp::gc(__FILE__);
          throw $e;
        }

        // Sybase 15 EED packets e.g.
        if (0 === $this->header['type']) {
          $this->header['type']= 0x04;
          $this->header['status']= 0x01;
        }

        $packet= $this->readFully($this->header['length'] - 8);

        // DEBUG Console::$err->writeLine("R->\n".self::dump($bytes.$packet));

        $this->buffer.= $packet;
      }

      return $length;
    }

    /**
     * Read a given number of bytes
     *
     * @param   int length
     * @return  string
     * @throws  rdbms.tds.TdsProtocolException
     */
    public function read($length) {
      $length= $this->read0($length);

      // Return chunk of specified length
      $chunk= substr($this->buffer, 0, $length);
      $this->buffer= substr($this->buffer, $length);
      return (string)$chunk;
    }

    /**
     * Creates a string representation of this stream
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(#'.$this->packetSize.")@{\n".self::dump($this->buffer)."\n}";
    }
  }
?>
