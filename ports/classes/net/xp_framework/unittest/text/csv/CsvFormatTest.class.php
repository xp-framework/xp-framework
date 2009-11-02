<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.csv.CsvFormat'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.CsvFormat
   */
  class CsvFormatTest extends TestCase {
  
    /**
     * Test default format uses ';' as delimiter and '"' as quote
     *
     */
    #[@test]
    public function defaultFormat() {
      $format= CsvFormat::$DEFAULT;
      $this->assertEquals(';', $format->getDelimiter());
      $this->assertEquals('"', $format->getQuote());
    }

    /**
     * Test pipes format uses '|' as delimiter and '"' as quote
     *
     */
    #[@test]
    public function pipesFormat() {
      $format= CsvFormat::$PIPES;
      $this->assertEquals('|', $format->getDelimiter());
      $this->assertEquals('"', $format->getQuote());
    }

    /**
     * Test commas format uses ',' as delimiter and '"' as quote
     *
     */
    #[@test]
    public function commasFormat() {
      $format= CsvFormat::$COMMAS;
      $this->assertEquals(',', $format->getDelimiter());
      $this->assertEquals('"', $format->getQuote());
    }

    /**
     * Test tabs format uses TAB as delimiter and '"' as quote
     *
     */
    #[@test]
    public function tabsFormat() {
      $format= CsvFormat::$TABS;
      $this->assertEquals("\t", $format->getDelimiter());
      $this->assertEquals('"', $format->getQuote());
    }

    /**
     * Test setQuote() and getQuote() method
     *
     */
    #[@test]
    public function quoteAccessors() {
      $format= new CsvFormat();
      $format->setQuote('`');
      $this->assertEquals('`', $format->getQuote());
    }

    /**
     * Test withQuote() and getQuote() method
     *
     */
    #[@test]
    public function withQuoteAccessor() {
      $format= new CsvFormat();
      $this->assertEquals($format, $format->withQuote('`'));
      $this->assertEquals('`', $format->getQuote());
    }

    /**
     * Test setDelimiter() and getDelimiter() method
     *
     */
    #[@test]
    public function delimiterAccessors() {
      $format= new CsvFormat();
      $format->setDelimiter(' ');
      $this->assertEquals(' ', $format->getDelimiter());
    }

    /**
     * Test withDelimiter() and getDelimiter() method
     *
     */
    #[@test]
    public function withDelimiterAccessor() {
      $format= new CsvFormat();
      $this->assertEquals($format, $format->withDelimiter(' '));
      $this->assertEquals(' ', $format->getDelimiter());
    }

    /**
     * Test setQuoting() and getQuoting() method
     *
     */
    #[@test]
    public function quotingAccessors() {
      $format= new CsvFormat();
      $format->setQuoting(text搾sv想uoting::$ALWAYS);
      $this->assertEquals(text搾sv想uoting::$ALWAYS, $format->getQuoting());
    }

    /**
     * Test withQuoting() and getQuoting() method
     *
     */
    #[@test]
    public function withQuotingAccessor() {
      $format= new CsvFormat();
      $this->assertEquals($format, $format->withQuoting(text搾sv想uoting::$ALWAYS));
      $this->assertEquals(text搾sv想uoting::$ALWAYS, $format->getQuoting());
    }

    /**
     * Test withQuote() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function quoteCharMayNotBeLongerThanOneCharacter() {
      create(new CsvFormat())->withQuote('Hello');
    }

    /**
     * Test withQuote() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function quoteCharMayNotBeEmpty() {
      create(new CsvFormat())->withQuote('');
    }

    /**
     * Test withDelimiter() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function delimiterCharMayNotBeLongerThanOneCharacter() {
      create(new CsvFormat())->withDelimiter('Hello');
    }

    /**
     * Test withDelimiter() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function delimiterCharMayNotBeEmpty() {
      create(new CsvFormat())->withDelimiter('');
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function defaultFormatUnchangeableBySetDelimiter() {
      CsvFormat::$DEFAULT->setDelimiter(',');
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test]
    public function withDelimiterClonesDefaults() {
      $format= CsvFormat::$DEFAULT->withDelimiter(',');
      $this->assertFalse($format === CsvFormat::$DEFAULT);
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function defaultFormatUnchangeableBySetQuote() {
      CsvFormat::$DEFAULT->setQuote("'");
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test]
    public function withQuoteClonesDefaults() {
      $format= CsvFormat::$DEFAULT->withQuote("'");
      $this->assertFalse($format === CsvFormat::$DEFAULT);
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test, @expect('lang.IllegalStateException')]
    public function defaultFormatUnchangeableBySetQuoting() {
      CsvFormat::$DEFAULT->setQuoting(text搾sv想uoting::$ALWAYS);
    }

    /**
     * Test default format is not changeable
     *
     */
    #[@test]
    public function withQuotingClonesDefaults() {
      $format= CsvFormat::$DEFAULT->withQuoting(text搾sv想uoting::$ALWAYS);
      $this->assertFalse($format === CsvFormat::$DEFAULT);
    }
  }
?>
