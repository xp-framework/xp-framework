<?php namespace net\xp_framework\unittest\remote;

use unittest\TestCase;
use io\streams\MemoryOutputStream;
use remote\protocol\ByteCountedString;


/**
 * Unit test for ByteCountedString class
 *
 * @see   xp://remote.ByteCountedString
 */
class ByteCountedStringTest extends TestCase {
  private static $CHUNK_STRING= '12345';
  private static $CHUNK_LENGTH= 5; // strlen(self::$CHUNK_STRING)
  private static $CHUNK_HEADER= 3;
  private static $mockSocket;

  #[@beforeClass]
  public static function defineMockSocket() {
    self::$mockSocket= \lang\ClassLoader::defineClass('net.xp_framework.unittest.remote.MockSocket', 'lang.Object', array(), '{
      public function __construct($bytes) {
        $this->bytes= $bytes;
        $this->offset= 0;
      }

      public function readBinary($size= 8192) {
        if ($this->offset > strlen($this->bytes)) return FALSE;
        $chunk= substr($this->bytes, $this->offset, $size);
        $this->offset+= strlen($chunk);
        return $chunk;
      }
    }');
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function empty_bstring_length() {
    $this->assertEquals(3, create(new ByteCountedString())->length());
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function length_of_single_chunk() {
    $this->assertEquals(
      self::$CHUNK_HEADER + self::$CHUNK_LENGTH, 
      create(new ByteCountedString(self::$CHUNK_STRING))->length(self::$CHUNK_LENGTH)
    );
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function length_of_single_chunk_with_umlaut() {
    $this->assertEquals(
      self::$CHUNK_HEADER + 2, 
      create(new ByteCountedString('ä'))->length(self::$CHUNK_LENGTH)
    );
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function length_of_two_chunks() {
    $this->assertEquals(
      (self::$CHUNK_HEADER + self::$CHUNK_LENGTH) * 2, 
      create(new ByteCountedString(self::$CHUNK_STRING.self::$CHUNK_STRING))->length(self::$CHUNK_LENGTH)
    );
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function length_of_two_chunks_minus_one_char() {
    $this->assertEquals(
      (self::$CHUNK_HEADER + self::$CHUNK_LENGTH) * 2 - 1, 
      create(new ByteCountedString(self::$CHUNK_STRING.substr(self::$CHUNK_STRING, 0, -1)))->length(self::$CHUNK_LENGTH)
    );
  }

  /**
   * Test length()
   *
   */
  #[@test]
  public function length_of_two_chunks_plus_one_char() {
    $this->assertEquals(
      (self::$CHUNK_HEADER + self::$CHUNK_LENGTH) * 2 + self::$CHUNK_HEADER + 1, 
      create(new ByteCountedString(self::$CHUNK_STRING.self::$CHUNK_STRING.'a'))->length(self::$CHUNK_LENGTH)
    );
  }

  /**
   * Assertion helper
   *
   * @param  string bytes
   * @param  remote.protocol.ByteCountedString bcs
   */
  protected function assertWrittenEquals($bytes, $bcs) {
    with ($s= new MemoryOutputStream()); {
      $bcs->writeTo($s, self::$CHUNK_LENGTH);
      $this->assertEquals(new \lang\types\Bytes($bytes), new \lang\types\Bytes($s->getBytes()));
    }
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_empty_bstring() {
    $this->assertWrittenEquals(
      "\x00\x00\x00",
      new ByteCountedString()
    );
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_single_chunk() {
    $this->assertWrittenEquals(
      "\x00".chr(self::$CHUNK_LENGTH)."\x00".self::$CHUNK_STRING,
      new ByteCountedString(self::$CHUNK_STRING)
    );
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_single_chunk_with_umlaut() {
    $this->assertWrittenEquals(
      "\x00\x02\x00\xc3\xa4",
      new ByteCountedString('ä')
    );
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_two_chunks() {
    $this->assertWrittenEquals(
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH)."\x00".self::$CHUNK_STRING,
      new ByteCountedString(self::$CHUNK_STRING.self::$CHUNK_STRING)
    );
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_two_chunks_minus_one_char() {
    $this->assertWrittenEquals(
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH - 1)."\x00".substr(self::$CHUNK_STRING, 0, -1),
      new ByteCountedString(self::$CHUNK_STRING.substr(self::$CHUNK_STRING, 0, -1))
    );
  }

  /**
   * Test writeTo()
   *
   */
  #[@test]
  public function write_two_chunks_plus_one_char() {
    $this->assertWrittenEquals(
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00\x01\x00a",
      new ByteCountedString(self::$CHUNK_STRING.self::$CHUNK_STRING.'a')
    );
  }

  /**
   * Assertion helper
   *
   * @param  string expected
   * @param  string bytes
   */
  protected function assertReadEquals($expected, $bytes) {
    with ($s= self::$mockSocket->newInstance($bytes)); {
      $this->assertEquals(new \lang\types\Bytes($expected), new \lang\types\Bytes(ByteCountedString::readFrom($s)));
    }
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_empty_bstring() {
    $this->assertReadEquals('', "\x00\x00\x00");
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_single_chunk() {
    $this->assertReadEquals(
      self::$CHUNK_STRING, 
      "\x00".chr(self::$CHUNK_LENGTH)."\x00".self::$CHUNK_STRING
    );
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_single_chunk_with_umlaut() {
    $this->assertReadEquals('ä', "\x00\x02\x00\xc3\xa4");
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_two_chunks() {
    $this->assertReadEquals(
      self::$CHUNK_STRING.self::$CHUNK_STRING, 
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH)."\x00".self::$CHUNK_STRING
    );
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_two_chunks_minus_one_char() {
    $this->assertReadEquals(
      self::$CHUNK_STRING.substr(self::$CHUNK_STRING, 0, -1),
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH - 1)."\x00".substr(self::$CHUNK_STRING, 0, -1)
    );
  }

  /**
   * Test readFrom()
   *
   */
  #[@test]
  public function read_two_chunks_plus_one_char() {
    $this->assertReadEquals(
      self::$CHUNK_STRING.self::$CHUNK_STRING.'a',
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00".chr(self::$CHUNK_LENGTH)."\x01".self::$CHUNK_STRING.
      "\x00\x01\x00a"
    );
  }
}
