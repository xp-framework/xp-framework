<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.regex.Pattern',
    'lang.types.String'
  );

  /**
   * TestCase for pattern class
   *
   * @purpose  Unit test
   */
  class PatternTest extends TestCase {

    /**
     * Tests the MatchResult::length() method
     *
     */
    #[@test]
    public function length() {
      $this->assertEquals(
        0, 
        Pattern::compile('ABC')->matches('123')->length()
      );
    }

    /**
     * Tests "." as a pattern with string primitive
     *
     */
    #[@test]
    public function stringPrimitiveInput() {
      $this->assertEquals(0, Pattern::compile('.')->matches('')->length());
      $this->assertEquals(1, Pattern::compile('.')->matches('a')->length());
      $this->assertEquals(2, Pattern::compile('.')->matches('ab')->length());
    }

    /**
     * Tests "." as a pattern with lang.types.String instances
     *
     */
    #[@test]
    public function stringObjectInput() {
      $this->assertEquals(0, Pattern::compile('.')->matches(new String(''))->length());
      $this->assertEquals(1, Pattern::compile('.')->matches(new String('a'))->length());
      $this->assertEquals(2, Pattern::compile('.')->matches(new String('ab'))->length());
    }

    /**
     * Tests unicode pattern
     *
     */
    #[@test]
    public function unicodePattern() {
      $this->assertEquals(
        array('GÃ¼n'), 
        Pattern::compile('.Ã¼.', Pattern::UTF8)->matches(new String('Günter'))->group(0)
      );
    }

    /**
     * Tests non-unicode pattern
     *
     */
    #[@test]
    public function nonUnicodePattern() {
      $this->assertEquals(
        array('Gün'), 
        Pattern::compile('.ü.')->matches(new String('Günter'))->group(0)
      );
    }

    /**
     * Tests the CASE_INSENSITIVE flag
     *
     */
    #[@test]
    public function caseInsensitive() {
      $this->assertEquals(
        1, 
        Pattern::compile('a', Pattern::CASE_INSENSITIVE)->matches('A')->length()
      );
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
     * Tests the MatchResult::group() method
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function nonExistantGroup() {
      Pattern::compile('H[ea]llo')->matches('Hello')->group(1);
    }

    /**
     * Tests the Pattern::equals() method
     *
     */
    #[@test]
    public function equality() {
      $this->assertEquals(
        Pattern::compile('[a-z]+'),
        Pattern::compile('[a-z]+')
      );
    }

    /**
     * Tests the Pattern::equals() method
     *
     */
    #[@test]
    public function unequality() {
      $this->assertNotEquals(
        Pattern::compile('[a-z]+', Pattern::CASE_INSENSITIVE),
        Pattern::compile('[a-z]+')
      );
    }

    /**
     * Tests the Pattern::toString() method
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->assertEquals(
        'text.regex.Pattern</[a-z]+/i>',
        Pattern::compile('[a-z]+', Pattern::CASE_INSENSITIVE)->toString()
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
