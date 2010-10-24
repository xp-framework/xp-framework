<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.TextReader',
    'io.streams.MemoryInputStream'
  );

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
      $this->assertEquals(new String('H'), $this->newReader('Hello')->read(1));
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function readOneUtf8() {
      $this->assertEquals(new String('Ü', 'iso-8859-1'), $this->newReader('Ãœbercoder', 'utf-8')->read(1));
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function readOneUtf8ThenRest() {
      $r= $this->newReader('Ãœbercoder', 'utf-8');
      $this->assertEquals(new String('Ü', 'iso-8859-1'), $r->read(1));
      $this->assertEquals(new String('bercoder'), $r->read(8));
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function readLength() {
      $this->assertEquals(new String('Hello'), $this->newReader('Hello')->read(5));
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function readLengthUtf8() {
      $this->assertEquals(new String('Übercoder', 'iso-8859-1'), $this->newReader('Ãœbercoder', 'utf-8')->read(9));
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
        $this->fail('No exception caught', NULL, 'lang.FormatException');
      } catch (FormatException $expected) {
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
    #[@test, @ignore('Supported now with Unicode'), @expect('lang.FormatException')]
    public function readUnconvertible() {
      $this->newReader('ËˆÃ§iËna', 'utf-8')->read();
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function read() {
      $this->assertEquals(new String('Hello'), $this->newReader('Hello')->read());
    }

    /**
     * Test reading a source returning encoded bytes only (no US-ASCII inbetween!)
     *
     */
    #[@test]
    public function encodedBytesOnly() {
      $this->assertEquals(
        new String(str_repeat('Ü', 1024), 'iso-8859-1'), 
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
      $this->assertEquals(new String('Hello'), $r->read(5));
      $this->assertNull($r->read());
    }

    /**
     * Test reading after EOF
     *
     */
    #[@test]
    public function readMultipleAfterEnd() {
      $r= $this->newReader('Hello');
      $this->assertEquals(new String('Hello'), $r->read(5));
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
      $this->assertEquals(new String('Hello'), $r->read(5));
      $this->assertNull($r->readLine());
    }

    /**
     * Test reading after EOF
     *
     */
    #[@test]
    public function readLineMultipleAfterEnd() {
      $r= $this->newReader('Hello');
      $this->assertEquals(new String('Hello'), $r->read(5));
      $this->assertNull($r->readLine());
      $this->assertNull($r->readLine());
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function readZero() {
      $this->assertEquals(new String(''), $this->newReader('Hello')->read(0));
    }
        
    /**
     * Test reading lines separated by "\n"
     *
     */
    #[@test]
    public function readLinesSeparatedByLineFeed() {
      $r= $this->newReader("Hello\nWorld");
      $this->assertEquals(new String('Hello'), $r->readLine());
      $this->assertEquals(new String('World'), $r->readLine());
      $this->assertNull($r->readLine());
    }
        
    /**
     * Test reading lines separated by "\r"
     *
     */
    #[@test]
    public function readLinesSeparatedByCarriageReturn() {
      $r= $this->newReader("Hello\rWorld");
      $this->assertEquals(new String('Hello'), $r->readLine());
      $this->assertEquals(new String('World'), $r->readLine());
      $this->assertNull($r->readLine());
    }
        
    /**
     * Test reading lines separated by "\r\n"
     *
     */
    #[@test]
    public function readLinesSeparatedByCRLF() {
      $r= $this->newReader("Hello\r\nWorld");
      $this->assertEquals(new String('Hello'), $r->readLine());
      $this->assertEquals(new String('World'), $r->readLine());
      $this->assertNull($r->readLine());
    }

    /**
     * Test reading an empty line
     *
     */
    #[@test]
    public function readEmptyLine() {
      $r= $this->newReader("Hello\n\nWorld");
      $this->assertEquals(new String('Hello'), $r->readLine());
      $this->assertEquals(new String(''), $r->readLine());
      $this->assertEquals(new String('World'), $r->readLine());
      $this->assertNull($r->readLine());
    }

    /**
     * Test reading lines
     *
     */
    #[@test]
    public function readLinesUtf8() {
      $r= $this->newReader("Ãœber\nCoder", 'utf-8');
      $this->assertEquals(new String('Über', 'iso-8859-1'), $r->readLine());
      $this->assertEquals(new String('Coder'), $r->readLine());
      $this->assertNull($r->readLine());
    }
    
    /**
     * Test reading lines w/ autodetected encoding at iso-8859-1
     *
     */
    #[@test]
    public function readLinesAutodetectIso88591() {
      $r= $this->newReader('Übercoder', NULL);
      $this->assertEquals(new String('Übercoder', 'iso-8859-1'), $r->readLine());
    }
    
    /**
     * Test reading from an encoding-autodetected stream when length of
     * data does is insufficient for autodetection.
     *
     */
    #[@test]
    public function readShortLinesAutodetectIso88591() {
      $r= $this->newReader('Ü', NULL);
      $this->assertEquals(new String('Ü', 'iso-8859-1'), $r->readLine());
    }
    
    
    /**
     * Test reading lines w/ autodetected encoding at utf-8
     *
     */
    #[@test]
    public function readLinesAutodetectUtf8() {
      $r= $this->newReader("\357\273\277\303\234bercoder", NULL);
      $this->assertEquals(new String('Übercoder', 'iso-8859-1'), $r->readLine());
    }

    /**
     * Test reading lines w/ autodetected encoding at utf-8
     *
     */
    #[@test]
    public function readLinesAutodetectUtf16BE() {
      $r= $this->newReader("\376\377\000\334\000b\000e\000r\000c\000o\000d\000e\000r", NULL);
      $this->assertEquals(new String('Übercoder', 'iso-8859-1'), $r->readLine());
    }
    
    /**
     * Test reading lines w/ autodetected encoding at utf-8
     *
     */
    #[@test]
    public function readLinesAutodetectUtf16LE() {
      $r= $this->newReader("\377\376\334\000b\000e\000r\000c\000o\000d\000e\000r\000", NULL);
      $this->assertEquals(new String('Übercoder', 'iso-8859-1'), $r->readLine());
    }

    /**
     * Test reading
     *
     */
    #[@test]
    public function bufferProblem() {
      $r= $this->newReader("Hello\rX");
      $this->assertEquals(new String('Hello'), $r->readLine());
      $this->assertEquals(new String('X'), $r->readLine());
      $this->assertNull($r->readLine());
    }
  }
?>
