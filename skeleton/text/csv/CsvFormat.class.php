<?php
/* This class is part of the XP framework
 *
 * $Id: CsvFormat.class.php 11510 2009-09-15 15:55:41Z friebe $ 
 */
 
  uses('text.csv.Quoting');

  /**
   * CSV format: Specifies which delimiter and quoting characters should
   * be used.
   *
   * Example:
   * <code>
   *   $format= create(new CsvFormat())->withDelimiter(';')->withQuote('"');
   * </code>
   *
   * Contains the following static members with predefined formats:
   * <ul>
   *   <li>CsvFormat::$DEFAULT - ';' and '"'</li>
   *   <li>CsvFormat::$PIPES   - '|' and '"'</li>
   *   <li>CsvFormat::$COMMAS  - ',' and '"'</li>
   *   <li>CsvFormat::$TABS    - [TAB] and '"'</li>
   * </ul>
   *
   * @test     xp://net.xp_framework.unittest.text.csv.CsvFormatTest
   * @see      xp://text.csv.CsvReader
   */
  class CsvFormat extends Object {
    protected $delimiter= '';
    protected $quote= '';
    protected $quoting= NULL;
    protected $final= FALSE;
    
    public static $DEFAULT= NULL;
    public static $PIPES= NULL;
    public static $COMMAS= NULL;
    public static $TABS= NULL;
    
    static function __static() {
      self::$DEFAULT= self::predefined(';', '"');
      self::$PIPES= self::predefined('|', '"');
      self::$COMMAS= self::predefined(',', '"');
      self::$TABS= self::predefined("\t", '"');
    }
    
    /**
     * Constructor
     *
     * @param   string delimiter
     * @param   string quote
     */
    public function __construct($delimiter= ';', $quote= '"') {
      $this->setDelimiter($delimiter);
      $this->setQuote($quote);
      $this->quoting= text·csv·Quoting::$DEFAULT;
    }
    
    /**
     * Constructor that creates final CsvFormat instances
     *
     * @param   string delimiter
     * @param   string quote
     */
    protected static function predefined($delimiter= ';', $quote= '"') {
      $s= new self($delimiter, $quote);
      $s->final= TRUE;
      return $s;
    }

    /**
     * Set delimiter character
     *
     * @param   string delimiter
     */
    public function setDelimiter($delimiter) {
      if ($this->final) {
        throw new IllegalStateException('Cannot change final object');
      }
      if (strlen($delimiter) != 1) {
        throw new IllegalArgumentException('Delimiter '.xp::stringOf($delimiter).' must be 1 character long');
      }
      $this->delimiter= $delimiter;
    }    

    /**
     * Set delimiter character and return this format object
     *
     * @param   string delimiter
     * @return  text.csv.CsvFormat self
     */
    public function withDelimiter($delimiter) {
      if ($this->final) {
        $self= clone $this;
        $self->final= FALSE;
      } else {
        $self= $this;
      }
      $self->setDelimiter($delimiter);
      return $self;
    }    

    /**
     * Returns delimiter character used in this format object
     *
     * @return  string
     */
    public function getDelimiter() {
      return $this->delimiter;
    }    

    /**
     * Set quoting character
     *
     * @param   string quote
     */
    public function setQuote($quote) {
      if ($this->final) {
        throw new IllegalStateException('Cannot change final object');
      }
      if (strlen($quote) != 1) {
        throw new IllegalArgumentException('Quote '.xp::stringOf($quote).' must be 1 character long');
      }
      $this->quote= $quote;
      return $this;
    }    

    /**
     * Set quoting character and return this format object
     *
     * @param   string quote
     * @return  text.csv.CsvFormat self
     */
    public function withQuote($quote) {
      if ($this->final) {
        $self= clone $this;
        $self->final= FALSE;
      } else {
        $self= $this;
      }
      $self->setQuote($quote);
      return $self;
    }    

    /**
     * Returns quoting character used in this format object
     *
     * @return  string
     */
    public function getQuote() {
      return $this->quote;
    }

    /**
     * Set quoting strategy
     *
     * @param   text.csv.QuotingStrategy quoting
     */
    public function setQuoting(QuotingStrategy $quoting) {
      if ($this->final) {
        throw new IllegalStateException('Cannot change final object');
      }
      $this->quoting= $quoting;
    }    

    /**
     * Set quoting strategy and return this format object
     *
     * @param   text.csv.QuotingStrategy quoting
     * @return  text.csv.CsvFormat self
     */
    public function withQuoting(QuotingStrategy $quoting) {
      if ($this->final) {
        $self= clone $this;
        $self->final= FALSE;
      } else {
        $self= $this;
      }
      $self->setQuoting($quoting);
      return $self;
    }    

    /**
     * Returns quoting strategy used in this format object
     *
     * @return  text.csv.QuotingStrategy
     */
    public function getQuoting() {
      return $this->quoting;
    }    
    
    /**
     * Format a value
     *
     * @param   string value
     * @return  string the formatted value and the delimiter
     */
    public function format($value) {
      if ($this->quoting->necessary($value, $this->delimiter, $this->quote)) {
        return $this->quote.str_replace($this->quote, $this->quote.$this->quote, $value).$this->quote.$this->delimiter;
      } else {
        return $value.$this->delimiter;
      }
    }
  }
?>
