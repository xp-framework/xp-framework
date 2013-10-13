<?php namespace net\xp_framework\unittest\io\streams;

use unittest\TestCase;
use io\streams\TextReader;
use io\streams\MemoryInputStream;


/**
 * TestCase
 *
 * @see      xp://io.streams.TextReader
 */
class TextReaderTest extends TestCase {

  /**
   * Returns a text reader for a given input string.
   *
   * @param   string str
   * @param   string charset
   * @return  io.streams.TextReader
   */
  protected function newReader($str, $charset= 'iso-8859-1') {
    return new TextReader(new MemoryInputStream($str), $charset);
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readOne() {
    $this->assertEquals('H', $this->newReader('Hello')->read(1));
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readOneUtf8() {
    $this->assertEquals('Ü', $this->newReader('Ãœbercoder', 'utf-8')->read(1));
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readLength() {
    $this->assertEquals('Hello', $this->newReader('Hello')->read(5));
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readLengthUtf8() {
    $this->assertEquals('Übercoder', $this->newReader('Ãœbercoder', 'utf-8')->read(9));
  }

  /**
   * Test reading
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function readBrokenUtf8() {
    $this->newReader('Hello Ã|', 'utf-8')->read(0x1000);
  }

  /**
   * Test reading
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function readMalformedUtf8() {
    $this->newReader('Hello Übercoder', 'utf-8')->read(0x1000);
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readingDoesNotContinueAfterBrokenCharacters() {
    $r= $this->newReader("Hello Übercoder\n".str_repeat('*', 512), 'utf-8');
    try {
      $r->read(1);
      $this->fail('No exception caught', null, 'lang.FormatException');
    } catch (\lang\FormatException $expected) {
      // OK
    }
    $this->assertNull($r->read(512));
  }

  /**
   * Test reading "'ç:ina" which contains two characters not convertible
   * to iso-8859-1, our internal encoding.
   *
   * @see     http://de.wikipedia.org/wiki/China (the word in the first square brackets on this page).
   */
  #[@test, @expect('lang.FormatException')]
  public function readUnconvertible() {
    $this->newReader('ËˆÃ§iËna', 'utf-8')->read();
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function read() {
    $this->assertEquals('Hello', $this->newReader('Hello')->read());
  }

  /**
   * Test reading a source returning encoded bytes only (no US-ASCII inbetween!)
   *
   */
  #[@test]
  public function encodedBytesOnly() {
    $this->assertEquals(
      str_repeat('Ü', 1024), 
      $this->newReader(str_repeat('Ãœ', 1024), 'utf-8')->read(1024)
    );
  }

  /**
   * Test reading after EOF
   *
   */
  #[@test]
  public function readAfterEnd() {
    $r= $this->newReader('Hello');
    $this->assertEquals('Hello', $r->read(5));
    $this->assertNull($r->read());
  }

  /**
   * Test reading after EOF
   *
   */
  #[@test]
  public function readMultipleAfterEnd() {
    $r= $this->newReader('Hello');
    $this->assertEquals('Hello', $r->read(5));
    $this->assertNull($r->read());
    $this->assertNull($r->read());
  }

  /**
   * Test reading after EOF
   *
   */
  #[@test]
  public function readLineAfterEnd() {
    $r= $this->newReader('Hello');
    $this->assertEquals('Hello', $r->read(5));
    $this->assertNull($r->readLine());
  }

  /**
   * Test reading after EOF
   *
   */
  #[@test]
  public function readLineMultipleAfterEnd() {
    $r= $this->newReader('Hello');
    $this->assertEquals('Hello', $r->read(5));
    $this->assertNull($r->readLine());
    $this->assertNull($r->readLine());
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readZero() {
    $this->assertEquals('', $this->newReader('Hello')->read(0));
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function readLineEmptyInput() {
    $this->assertNull($this->newReader('')->readLine());
  }

  /**
   * Test reading lines separated by "\n", "\r" and "\r\n"
   *
   */
  #[@test, @values(array(
  #  "Hello\nWorld\n", "Hello\rWorld\r", "Hello\r\nWorld\r\n",
  #  "Hello\nWorld", "Hello\rWorld", "Hello\r\nWorld"
  #))]
  public function readLines($value) {
    $r= $this->newReader($value);
    $this->assertEquals('Hello', $r->readLine());
    $this->assertEquals('World', $r->readLine());
    $this->assertNull($r->readLine());
  }

  /**
   * Test reading lines with one character separated by "\n", "\r" and "\r\n"
   *
   */
  #[@test, @values(array(
  #  "1\n2\n", "1\r2\r", "1\r\n2\r\n",
  #  "1\n2", "1\r2", "1\r\n2\r\n"
  #))]
  public function readLinesWithSingleCharacter($value) {
    $r= $this->newReader($value);
    $this->assertEquals('1', $r->readLine());
    $this->assertEquals('2', $r->readLine());
    $this->assertNull($r->readLine());
  }

  /**
   * Test reading an empty line
   *
   */
  #[@test]
  public function readEmptyLine() {
    $r= $this->newReader("Hello\n\nWorld");
    $this->assertEquals('Hello', $r->readLine());
    $this->assertEquals('', $r->readLine());
    $this->assertEquals('World', $r->readLine());
    $this->assertNull($r->readLine());
  }

  /**
   * Test reading lines
   *
   */
  #[@test]
  public function readLinesUtf8() {
    $r= $this->newReader("Ãœber\nCoder", 'utf-8');
    $this->assertEquals('Über', $r->readLine());
    $this->assertEquals('Coder', $r->readLine());
    $this->assertNull($r->readLine());
  }
  
  /**
   * Test reading lines w/ autodetected encoding at iso-8859-1
   *
   */
  #[@test]
  public function readLinesAutodetectIso88591() {
    $r= $this->newReader('Übercoder', null);
    $this->assertEquals('Übercoder', $r->readLine());
  }
  
  /**
   * Test reading from an encoding-autodetected stream when length of
   * data does is insufficient for autodetection.
   *
   */
  #[@test]
  public function readShortLinesAutodetectIso88591() {
    $r= $this->newReader('Ü', null);
    $this->assertEquals('Ü', $r->readLine());
  }
  
  
  /**
   * Test reading lines w/ autodetected encoding at utf-8
   *
   */
  #[@test]
  public function readLinesAutodetectUtf8() {
    $r= $this->newReader("\357\273\277\303\234bercoder", null);
    $this->assertEquals('Übercoder', $r->readLine());
  }

  /**
   * Test reading lines w/ autodetected encoding at utf-8
   *
   */
  #[@test]
  public function autodetectUtf8() {
    $r= $this->newReader("\357\273\277\303\234bercoder", null);
    $this->assertEquals('utf-8', $r->charset());
  }

  /**
   * Test reading lines w/ autodetected encoding at utf-16be
   *
   */
  #[@test]
  public function readLinesAutodetectUtf16BE() {
    $r= $this->newReader("\376\377\000\334\000b\000e\000r\000c\000o\000d\000e\000r", null);
    $this->assertEquals('Übercoder', $r->readLine());
  }

  /**
   * Test reading lines w/ autodetected encoding at utf-16be
   *
   */
  #[@test]
  public function autodetectUtf16Be() {
    $r= $this->newReader("\376\377\000\334\000b\000e\000r\000c\000o\000d\000e\000r", null);
    $this->assertEquals('utf-16be', $r->charset());
  }
  
  /**
   * Test reading lines w/ autodetected encoding at utf-16le
   *
   */
  #[@test]
  public function readLinesAutodetectUtf16Le() {
    $r= $this->newReader("\377\376\334\000b\000e\000r\000c\000o\000d\000e\000r\000", null);
    $this->assertEquals('Übercoder', $r->readLine());
  }

  /**
   * Test reading lines w/ autodetected encoding at utf-16le
   *
   */
  #[@test]
  public function autodetectUtf16Le() {
    $r= $this->newReader("\377\376\334\000b\000e\000r\000c\000o\000d\000e\000r\000", null);
    $this->assertEquals('utf-16le', $r->charset());
  }

  /**
   * Test reading lines w/ autodetected encoding at iso-8859-1
   *
   */
  #[@test]
  public function defaultCharsetIsIso88591() {
    $r= $this->newReader('Übercoder', null);
    $this->assertEquals('iso-8859-1', $r->charset());
  }

  /**
   * Test reading
   *
   */
  #[@test]
  public function bufferProblem() {
    $r= $this->newReader("Hello\rX");
    $this->assertEquals('Hello', $r->readLine());
    $this->assertEquals('X', $r->readLine());
    $this->assertNull($r->readLine());
  }

  /**
   * Test closing a reader twice has no effect.
   *
   * @see   xp://lang.Closeable#close
   */
  #[@test]
  public function closingTwice() {
    $r= $this->newReader('');
    $r->close();
    $r->close();
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function reset() {
    $r= $this->newReader('ABC');
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));

  }
  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetWithBuffer() {
    $r= $this->newReader("Line 1\rLine 2");
    $this->assertEquals('Line 1', $r->readLine());    // We have "\n" in the buffer
    $r->reset();
    $this->assertEquals('Line 1', $r->readLine());
    $this->assertEquals('Line 2', $r->readLine());
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf8() {
    $r= $this->newReader("\357\273\277ABC", null);
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf8WithoutBOM() {
    $r= $this->newReader('ABC', 'utf-8');
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf16Le() {
    $r= $this->newReader("\377\376A\000B\000C\000", null);
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf16LeWithoutBOM() {
    $r= $this->newReader("A\000B\000C\000", 'utf-16le');
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf16Be() {
    $r= $this->newReader("\376\377\000A\000B\000C", null);
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test]
  public function resetUtf16BeWithoutBOM() {
    $r= $this->newReader("\000A\000B\000C", 'utf-16be');
    $this->assertEquals('ABC', $r->read(3));
    $r->reset();
    $this->assertEquals('ABC', $r->read(3));
  }

  /**
   * Test resetting a reader
   *
   */
  #[@test, @expect(class= 'io.IOException', withMessage= 'Underlying stream does not support seeking')]
  public function resetUnseekable() {
    $r= new TextReader(newinstance('io.streams.InputStream', array(), '{
      public function read($size= 8192) { return NULL; }
      public function available() { return 0; }
      public function close() { }
    }'));
    $r->reset();
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readOneWithAutoDetectedIso88591Charset() {
    $this->assertEquals('H', $this->newReader('Hello', null)->read(1));
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readOneWithAutoDetectedUtf16BECharset() {
    $this->assertEquals('H', $this->newReader("\376\377\0H\0e\0l\0l\0o", null)->read(1));
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readOneWithAutoDetectedUtf16LECharset() {
    $this->assertEquals('H', $this->newReader("\377\376H\0e\0l\0l\0o\0", null)->read(1));
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readOneWithAutoDetectedUtf8Charset() {
    $this->assertEquals('H', $this->newReader("\357\273\277Hello", null)->read(1));
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readLineWithAutoDetectedIso88591Charset() {
    $this->assertEquals('H', $this->newReader("H\r\n", null)->readLine());
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readLineWithAutoDetectedUtf16BECharset() {
    $this->assertEquals('H', $this->newReader("\376\377\0H\0\r\0\n", null)->readLine());
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readLineWithAutoDetectedUtf16LECharset() {
    $this->assertEquals('H', $this->newReader("\377\376H\0\r\0\n\0", null)->readLine());
  }

  /**
   * Test reading after character set auto-detection
   */
  #[@test]
  public function readLineWithAutoDetectedUtf8Charset() {
    $this->assertEquals('H', $this->newReader("\357\273\277H\r\n", null)->readLine());
  }

  /**
   * Test reading
   */
  #[@test]
  public function readLineEmptyInputWithAutoDetectedIso88591Charset() {
    $this->assertNull($this->newReader('', null)->readLine());
  }

  /**
   * Test reading
   */
  #[@test, @values(array("\377", "\377\377", "\377\377\377"))]
  public function readNonBOMInputWithAutoDetectedIso88591Charset($value) {
    $this->assertEquals($value, $this->newReader($value, null)->read(0xFF));
  }

  /**
   * Test reading
   */
  #[@test, @values(array("\377", "\377\377", "\377\377\377"))]
  public function readLineNonBOMInputWithAutoDetectedIso88591Charset($value) {
    $this->assertEquals($value, $this->newReader($value, null)->readLine());
  }
}
