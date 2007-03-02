<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.regex.Pattern'
  );

  /**
   * TestCase for pattern class
   *
   * @purpose  Unit test
   */
  class PatternTest extends TestCase {
  
    /**
     * Tests 
     *
     */
    #[@test]
    public function pattern() {
      $this->assertEquals(1, Pattern::compile('.')->matches('a')->length());
      $this->assertEquals(2, Pattern::compile('.')->matches('ab')->length());
    }

    /**
     * Tests the CASE_INSENSITIVE flag
     *
     */
    #[@test]
    public function caseInsensitive() {
      $this->assertEquals(1, Pattern::compile('a', Pattern::CASE_INSENSITIVE)->matches('A')->length());
    }

    /**
     * Tests the MatchResult::groups() method
     *
     */
    #[@test]
    public function groups() {
      $this->assertEquals(
        array(array('Hello')), 
        Pattern::compile('H[ea]llo')->matches('Hello')->groups()
      );
    }

    /**
     * Tests the MatchResult::group() method
     *
     */
    #[@test]
    public function group() {
      $this->assertEquals(
        array('Hello'), 
        Pattern::compile('H[ea]llo')->matches('Hello')->group(0)
      );
    }

    /**
     * Test a pattern with a missing ")" at the end throws a FormatException
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalPattern() {
      Pattern::compile('(');
    }
  }
?>
