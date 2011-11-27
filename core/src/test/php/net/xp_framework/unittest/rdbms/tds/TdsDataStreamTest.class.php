<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'rdbms.tds.TdsDataStream',
    'peer.Socket'
  );

  /**
   * TestCase
   *
   * @see   xp://rdbms.tds.TdsDataStream
   */
  class TdsDataStreamTest extends TestCase {
    protected static $sock;
  
    #[@beforeClass]
    public static function mockSocket() {
      self::$sock= ClassLoader::defineClass('net.xp_framework.unittest.rdbms.tds.MockTdsSocket', 'peer.Socket', array(), '{
        protected $bytes;
        protected $offset= 0;
        
        public function __construct($bytes) {
          $this->bytes= $bytes;
        }
        
        public function readBinary($l) {
          $chunk= substr($this->bytes, $this->offset, $l);
          $this->offset+= $l;
          return $chunk;
        }
      }');
    }
  
    /**
     * Creates a new TdsDataStream instance
     *
     * @param   string bytws
     */
    public function newDataStream($bytes) {
      return new TdsDataStream(self::$sock->newInstance($bytes));
    }
    
    /**
     * Creates a TDS packet header with a given length
     *
     * @param   int length length of data
     * @param   bool last default TRUE
     * @return  string
     */
    protected function headerWith($length, $last= TRUE) {
      return pack('CCnnCc', 0x04, $last ? 0x01 : 0x00, $length + 8, 0x00, 0x01, 0x00);
    }

    /**
     * Test
     *
     */
    #[@test, @expect('rdbms.tds.TdsProtocolException')]
    public function nullHeader() { 
      $this->newDataStream(NULL)->read(1);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function readOneZeroLength() { 
      $this->assertEquals('', $this->newDataStream($this->headerWith(0))->read(1));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readAllZeroLength() { 
      $this->assertEquals('', $this->newDataStream($this->headerWith(0))->read(-1));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readLength() { 
      $this->assertEquals('Test', $this->newDataStream($this->headerWith(4).'Test')->read(4));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readMore() { 
      $this->assertEquals('Test', $this->newDataStream($this->headerWith(4).'Test')->read(1000));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readAll() { 
      $this->assertEquals('Test', $this->newDataStream($this->headerWith(4).'Test')->read(-1));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readLengthSpanningTwoPackets() {
      $packets= (
        $this->headerWith(2, FALSE).'Te'.
        $this->headerWith(2, TRUE).'st'
      );
      $this->assertEquals('Test', $this->newDataStream($packets)->read(4));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readMoreSpanningTwoPackets() {
      $packets= (
        $this->headerWith(2, FALSE).'Te'.
        $this->headerWith(2, TRUE).'st'
      );
      $this->assertEquals('Test', $this->newDataStream($packets)->read(1000));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function readAllSpanningTwoPackets() {
      $packets= (
        $this->headerWith(2, FALSE).'Te'.
        $this->headerWith(2, TRUE).'st'
      );
      $this->assertEquals('Test', $this->newDataStream($packets)->read(-1));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getString() {
      $str= $this->newDataStream($this->headerWith(9)."\x04T\x00e\x00s\x00t\x00");
      $this->assertEquals('Test', $str->getString($str->getByte()));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function beginReturnsMessageType() {
      $str= $this->newDataStream($this->headerWith(1)."\xAA");
      $this->assertEquals(0x04, $str->begin());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function beginDoesNotDiscardFirstByte() {
      $str= $this->newDataStream($this->headerWith(1)."\xAA");
      $str->begin();
      $this->assertEquals("\xAA", $str->getToken());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function beginDoesNotDiscardFirstBytes() {
      $str= $this->newDataStream($this->headerWith(2)."\xAA\xA2");
      $str->begin();
      $this->assertEquals("\xAA", $str->getToken());
      $this->assertEquals("\xA2", $str->getToken());
    }
  }
?>
