<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvListReader',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvListReader
   */
  class CsvListReaderTest extends TestCase {

    /**
     * Creates a new list reader
     *
     * @param   string str
     * @param   text.csv.CsvFormat format
     * @return  text.csv.CsvListReader
     */
    protected function newReader($str, CsvFormat $format= NULL) {
      return new CsvListReader(new TextReader(new MemoryInputStream($str)), $format);
    }
  
    /**
     * Test reading a single line
     *
     */
    #[@test]
    public function readLine() {
      $in= $this->newReader('Timm;Karlsruhe;76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a single line
     *
     */
    #[@test]
    public function readLineDelimitedByCommas() {
      $in= $this->newReader('Timm,Karlsruhe,76137', create(new CsvFormat())->withDelimiter(','));
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a single line
     *
     */
    #[@test]
    public function readLineDelimitedByPipes() {
      $in= $this->newReader('Timm|Karlsruhe|76137', create(new CsvFormat())->withDelimiter('|'));
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading an empty input
     *
     */
    #[@test]
    public function readEmpty() {
      $in= $this->newReader('');
      $this->assertNull($in->read());
    }

    /**
     * Test reading multiple lines
     *
     */
    #[@test]
    public function readMultipleLines() {
      $in= $this->newReader('Timm;Karlsruhe;76137'."\n".'Alex;Karlsruhe;76131');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
      $this->assertEquals(array('Alex', 'Karlsruhe', '76131'), $in->read());
    }

    /**
     * Test getHeaders() method
     *
     */
    #[@test]
    public function getHeaders() {
      $in= $this->newReader('name;city;zip'."\n".'Alex;Karlsruhe;76131');
      $this->assertEquals(array('name', 'city', 'zip'), $in->getHeaders());
      $this->assertEquals(array('Alex', 'Karlsruhe', '76131'), $in->read());
    }

    /**
     * Test getHeaders() method
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function cannotGetHeadersAfterReading() {
      $in= $this->newReader('Timm;Karlsruhe;76137');
      $in->read();
      $in->getHeaders();
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function leadingWhitespace() {
      $in= $this->newReader(' Timm;Karlsruhe;76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function leadingTab() {
      $in= $this->newReader("\tTimm;Karlsruhe;76137");
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function leadingTabAndWhiteSpace() {
      $in= $this->newReader("\t  Timm;Karlsruhe;76137");
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function leadingWhitespaces() {
      $in= $this->newReader('Timm;    Karlsruhe;    76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function leadingTabs() {
      $in= $this->newReader("Timm;\tKarlsruhe;\t76137");
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function trailingWhitespace() {
      $in= $this->newReader('Timm ;Karlsruhe;76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function trailingTab() {
      $in= $this->newReader("Timm\t;Karlsruhe;76137");
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function surroundingWhitespace() {
      $in= $this->newReader('Timm   ;   Karlsruhe   ;   76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test whitespace is ignored
     *
     */
    #[@test]
    public function whiteSpaceAndEmpty() {
      $in= $this->newReader('       ;   Karlsruhe   ;   76137');
      $this->assertEquals(array('', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValue() {
      $in= $this->newReader('"Timm";Karlsruhe;76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValueWithSurroundingWhitespace() {
      $in= $this->newReader('   "Timm"    ;Karlsruhe;76137');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValueIncludingWhitespace() {
      $in= $this->newReader('"   Timm    ";Karlsruhe;76137');
      $this->assertEquals(array('   Timm    ', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValues() {
      $in= $this->newReader('"Timm";"Karlsruhe";"76137"');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedWithSingleQuotes() {
      $in= $this->newReader("Timm;'Karlsruhe';76137", create(new CsvFormat())->withQuote("'"));
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value containing the separator character
     *
     */
    #[@test]
    public function readQuotedValueWithSeparator() {
      $in= $this->newReader('"Friebe;Timm";Karlsruhe;76137');
      $this->assertEquals(array('Friebe;Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value containing the separator character
     *
     */
    #[@test]
    public function readQuotedValueWithSeparatorInMiddle() {
      $in= $this->newReader('Timm;"Karlsruhe;Germany";76137');
      $this->assertEquals(array('Timm', 'Karlsruhe;Germany', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value containing the separator character
     *
     */
    #[@test]
    public function readQuotedValueWithSeparatorAtEnd() {
      $in= $this->newReader('Timm;Karlsruhe;"76131;76135;76137"');
      $this->assertEquals(array('Timm', 'Karlsruhe', '76131;76135;76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValueWithQuotes() {
      $in= $this->newReader('"""Hello""";Karlsruhe;76137');
      $this->assertEquals(array('"Hello"', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readEmptyQuotedValue() {
      $in= $this->newReader('"";Karlsruhe;76137');
      $this->assertEquals(array('', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function readQuotedValueWithQuotesInside() {
      $in= $this->newReader('"Timm""Karlsruhe";76137');
      $this->assertEquals(array('Timm"Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function quotesInsideUnquoted() {
      $in= $this->newReader('He said "Hello";Karlsruhe;76137');
      $this->assertEquals(array('He said "Hello"', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading a quoted value
     *
     */
    #[@test]
    public function quoteInsideUnquoted() {
      $in= $this->newReader('A single " is OK;Karlsruhe;76137');
      $this->assertEquals(array('A single " is OK', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test multi-line string. From the specification: "An entry may 
     * contain newlines in which case the whole entry is enclosed in 
     * quotation marks".
     *
     */
    #[@test]
    public function multiLine() {
      $in= $this->newReader(
        "14:30-15:30;Development;'- Fix unittests\n- QA: Apidoc'",
        create(new CsvFormat())->withQuote("'")
      );
      $this->assertEquals(
        array('14:30-15:30', 'Development', "- Fix unittests\n- QA: Apidoc"), 
        $in->read()
      );
    }

    /**
     * Test multi-line string. From the specification: "An entry may 
     * contain newlines in which case the whole entry is enclosed in 
     * quotation marks".
     *
     */
    #[@test]
    public function multiLines() {
      $in= $this->newReader(
        "14:30-15:30;Development;'- Fix unittests\n- QA: Apidoc'\n15:30-15:49;Report;- Tests",
        create(new CsvFormat())->withQuote("'")
      );
      $this->assertEquals(array('14:30-15:30', 'Development', "- Fix unittests\n- QA: Apidoc"), $in->read());
      $this->assertEquals(array('15:30-15:49', 'Report', '- Tests'), $in->read());
    }

    /**
     * Test quoting of only partial values
     *
     */
    #[@test, @ignore('Is this really allowed?')]
    public function partialQuoting() {
      $in= $this->newReader('"Timm"|"Karlsruhe";76137');
      $this->assertEquals(array('Timm|Karlsruhe', '76131'), $in->read());
    }

    /**
     * Test quoting of only partial values
     *
     */
    #[@test, @ignore('Is this really allowed?')]
    public function partialQuotingDelimiter() {
      $in= $this->newReader('Timm";"Karlsruhe;76137');
      $this->assertEquals(array('Timm;Karlsruhe', '76131'), $in->read());
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuote() {
      $this->newReader('"Unterminated;Karlsruhe;76131')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteInTheMiddle() {
      $this->newReader('Timm;"Unterminated;76131')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteRightBeforeSeparator() {
      $this->newReader('";Karlsruhe;76131')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteInTheMiddleRightBeforeSeparator() {
      $this->newReader('Timm;";76131')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteAtEnd() {
      $this->newReader('A;B;"')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteAtEndWithTrailingSeparator() {
      $this->newReader('A;B;";')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unterminatedQuoteAtBeginning() {
      $this->newReader('"')->read();
    }

    /**
     * Test unterminated quoted value detection
     *
     */
    #[@test]
    public function readingContinuesAfterBrokenLine() {
      $in= $this->newReader('"Hello"-;Karlsruhe;76131'."\n".'Timm;Karlsruhe;76137');
      try {
        $in->read();
        $this->fail('Unterminated literal not detected', NULL, 'lang.FormatException');
      } catch (FormatException $expected) { }
      $this->assertEquals(array('Timm', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading an empty value
     *
     */
    #[@test]
    public function readEmptyValue() {
      $in= $this->newReader('Timm;;76137');
      $this->assertEquals(array('Timm', '', '76137'), $in->read());
    }

    /**
     * Test reading an empty value
     *
     */
    #[@test]
    public function readEmptyValueAtBeginning() {
      $in= $this->newReader(';Karlsruhe;76137');
      $this->assertEquals(array('', 'Karlsruhe', '76137'), $in->read());
    }

    /**
     * Test reading an empty value
     *
     */
    #[@test]
    public function readEmptyValueAtEnd() {
      $in= $this->newReader('Timm;Karlsruhe;');
      $this->assertEquals(array('Timm', 'Karlsruhe', ''), $in->read());
    }

    /**
     * Test reading an empty value
     *
     */
    #[@test]
    public function readEmptyValueAtEndWithTrailingDelimiter() {
      $in= $this->newReader('Timm;Karlsruhe;;');
      $this->assertEquals(array('Timm', 'Karlsruhe', '', ''), $in->read());
    }

    /**
     * Test illegal quoting
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalQuoting() {
      $this->newReader('"Timm"Karlsruhe";76137')->read();
    }

    /**
     * Test TAB as delimiter
     *
     */
    #[@test]
    public function tabDelimiter() {
      $in= $this->newReader("A\tB\tC", CsvFormat::$TABS);
      $this->assertEquals(array('A', 'B', 'C'), $in->read());
    }

    /**
     * Test SPACE as delimiter
     *
     */
    #[@test]
    public function spaceDelimiter() {
      $in= $this->newReader('A B C', create(new CsvFormat())->withDelimiter(' '));
      $this->assertEquals(array('A', 'B', 'C'), $in->read());
    }

    /**
     * Test example from Wikipedia article on the CSV file format
     *
     * @see   http://en.wikipedia.org/wiki/Comma-separated_values
     */
    #[@test]
    public function wikipediaExample() {
      $r= $this->newReader(
        '1997,Ford,E350,"ac, abs, moon",3000.00'."\n".
        '1999,Chevy,"Venture ""Extended Edition""","",4900.00'."\n".
        '1996,Jeep,Grand Cherokee,"MUST SELL!'."\n".
        'air, moon roof, loaded",4799.00'."\n",
        CsvFormat::$COMMAS
      );
      $this->assertEquals(array('1997', 'Ford', 'E350', 'ac, abs, moon', '3000.00'), $r->read());
      $this->assertEquals(array('1999', 'Chevy', 'Venture "Extended Edition"', '', '4900.00'), $r->read());
      $this->assertEquals(array('1996', 'Jeep', 'Grand Cherokee', "MUST SELL!\nair, moon roof, loaded", '4799.00'), $r->read());
      $this->assertNull($r->read());
    }
  }
?>
