<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.csv.Quoting'
  );

  /**
   * TestCase
   *
   * @see      xp://text.csv.Quoting
   */
  class QuotingTest extends TestCase {
    private static $never;

    /**
     * Creating a quoting strategy that never quotes anything. This is
     * for unittesting purposes only, such a strategy would not make
     * sense in real-life situations!
     */
    #[@beforeClass]
    public static function neverQuotingStrategy() {
      self::$never= newinstance('text.csv.QuotingStrategy', array(), '{
        public function necessary($value, $delimiter, $quote) {
          return FALSE;
        }
      }');
    }

    /**
     * Returns quoting strategies
     *
     * @return  var[]
     */
    public function quotingStrategies() {
      return array(text搾sv想uoting::$DEFAULT, text搾sv想uoting::$EMPTY);
    }

    #[@test, @values('quotingStrategies')]
    public function delimiter_is_quoted($strategy) {
      $this->assertTrue($strategy->necessary(';', ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function quote_is_quoted($strategy) {
      $this->assertTrue($strategy->necessary('"', ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function mac_newline_is_quoted($strategy) {
      $this->assertTrue($strategy->necessary("\r", ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function unix_newline_is_quoted($strategy) {
      $this->assertTrue($strategy->necessary("\n", ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function windows_newline_is_quoted($strategy) {
      $this->assertTrue($strategy->necessary("\r\n", ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function single_word_and_newline_is_not_quoted($strategy) {
      $this->assertTrue($strategy->necessary("Test\n", ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function single_word_is_not_quoted($strategy) {
      $this->assertFalse($strategy->necessary('Test', ';', '"'));
    }

    #[@test, @values('quotingStrategies')]
    public function two_words_separated_by_space_are_not_quoted($strategy) {
      $this->assertFalse($strategy->necessary('Hello World', ';', '"'));
    }

    /**
     * Test empty values are not quoted in the default strategy
     */
    #[@test]
    public function emtpy_string_not_quoted_with_default() {
      $this->assertFalse(text搾sv想uoting::$DEFAULT->necessary('', ';', '"'));
    }

    /**
     * Test empty values are quoted in the empty strategy
     */
    #[@test]
    public function emtpy_string_quoted_with_empty() {
      $this->assertTrue(text搾sv想uoting::$EMPTY->necessary('', ';', '"'));
    }

    /**
     * Test any of the above are quoted in the "always" strategy
     */
    #[@test, @values(array('', ';', '"', "\r", "\n", "\r\n", 'A', 'Hello'))]
    public function anything_is_quoted_with_always_strategy($value) {
      $this->assertTrue(text搾sv想uoting::$ALWAYS->necessary($value, ';', '"'), $value);
    }

    /**
     * Test none of the above are quoted in the "never" strategy
     */
    #[@test, @values(array('', ';', '"', "\r", "\n", "\r\n", 'A', 'Hello'))]
    public function nothing_is_quoted_with_never_strategy($value) {
      $this->assertFalse(self::$never->necessary($value, ';', '"'));
    }
  }
?>
