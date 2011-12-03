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
  
    /**
     * Defines the mock socket class necessary for these tests
     *
     */
    #[@beforeClass]
    public static function mockSocket() {
      self::$sock= ClassLoader::defineClass('net.xp_framework.unittest.rdbms.tds.MockTdsSocket', 'peer.Socket', array(), '{
        public $bytes;
        protected $offset= 0;
        
        public function __construct($bytes= "") {
          $this->bytes= $bytes;
        }

        public function write($bytes) {
          $this->bytes.= $bytes;
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
     * @param   string bytes
     * @param   int packetSize default 512
     * @return  rdbms.tds.TdsDataStream
     */
    public function newDataStream($bytes= '', $packetSize= 512) {
      return new TdsDataStream(self::$sock->newInstance($bytes), $packetSize);
    }
    
    /**
     * Creates a TDS packet header with a given length
     *
     * @param   int length length of data
     * @param   bool last default TRUE
     * @return  string
     */
    protected function headerWith($length, $last= TRUE) {
      return pack('CCnnCc', 0x04, $last ? 0x01 : 0x00, $length + 8, 0x00, 0x00, 0x00);
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
     * Test getString()
     *
     */
    #[@test]
    public function getString() {
      $str= $this->newDataStream($this->headerWith(9)."\x04T\x00e\x00s\x00t\x00");
      $this->assertEquals('Test', $str->getString($str->getByte()));
    }

    /**
     * Test getToken()
     *
     */
    #[@test]
    public function getToken() {
      $str= $this->newDataStream($this->headerWith(1)."\x07");
      $this->assertEquals("\x07", $str->getToken());
    }

    /**
     * Test getByte()
     *
     */
    #[@test]
    public function getByte() {
      $str= $this->newDataStream($this->headerWith(1)."\x07");
      $this->assertEquals(0x07, $str->getByte());
    }

    /**
     * Test getShort()
     *
     */
    #[@test]
    public function getShort() {
      $str= $this->newDataStream($this->headerWith(2)."\x07\x08");
      $this->assertEquals(0x0807, $str->getShort());
    }

    /**
     * Test getLong()
     *
     */
    #[@test]
    public function getLong() {
      $str= $this->newDataStream($this->headerWith(4)."\x05\x06\x07\x08");
      $this->assertEquals(0x8070605, $str->getLong());
    }

    /**
     * Test get()
     *
     */
    #[@test]
    public function get() {
      $str= $this->newDataStream($this->headerWith(4)."\x05\x06\x07\x08");
      $this->assertEquals(
        array('length' => 0x05, 'flags' => 0x06, 'state' => 0x0807),
        $str->get("Clength/Cflags/vstate", 4)
      );
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
    
    /**
     * Assertion helper
     *
     * @param   string bytes
     * @param   rdbms.tds.TdsDataStream str
     * @throws  unittest.AssertionFailedError
     */
    protected function assertBytes($bytes, $str) {
      $field= $str->getClass()->getField('sock')->setAccessible(TRUE);
      $this->assertEquals(new Bytes($bytes), new Bytes($field->get($str)->bytes));
    }

    /**
     * Test write() method
     *
     */
    #[@test, @expect(class = 'lang.IllegalArgumentException', withMessage= '/must be at least 9/')]
    public function illegalPacketSize() {
      $this->newDataStream('', 1);
    }

    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeBytes() {
      $str= $this->newDataStream();
      $str->write(0x04, 'Login');
      $this->assertBytes($this->headerWith(5).'Login', $str);
    }


    /**
     * Test write() method
     *
     */
    #[@test]
    public function writeBytesSpanningMultiplePackets() {
      $str= $this->newDataStream('', 10);
      $str->write(0x04, 'Test');
      $this->assertBytes(
        $this->headerWith(2, FALSE).'Te'.$this->headerWith(2, TRUE).'st', 
        $str
      );
    }
  }
?>
