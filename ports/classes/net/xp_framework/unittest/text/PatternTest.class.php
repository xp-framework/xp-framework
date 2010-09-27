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
        Pattern::compile('ABC')->match('123')->length()
      );
    }

    /**
     * Tests the MatchResult::matches() method
     *
     */
    #[@test]
    public function isMatched() {
      $this->assertTrue(Pattern::compile('a+')->matches('aaa'));
    }

    /**
     * Tests the MatchResult::matches() method
     *
     */
    #[@test]
    public function isNotMatched() {
      $this->assertFalse(Pattern::compile('a+')->matches('bbb'));
    }

    /**
     * Tests "." as a pattern with string primitive
     *
     */
    #[@test]
    public function stringPrimitiveInput() {
      $this->assertEquals(0, Pattern::compile('.')->match('')->length());
      $this->assertEquals(1, Pattern::compile('.')->match('a')->length());
      $this->assertEquals(2, Pattern::compile('.')->match('ab')->length());
    }

    /**
     * Tests "." as a pattern with lang.types.String instances
     *
     */
    #[@test]
    public function stringObjectInput() {
      $this->assertEquals(0, Pattern::compile('.')->match(new String(''))->length());
      $this->assertEquals(1, Pattern::compile('.')->match(new String('a'))->length());
      $this->assertEquals(2, Pattern::compile('.')->match(new String('ab'))->length());
    }

    /**
     * Tests unicode pattern
     *
     */
    #[@test]
    public function unicodePattern() {
      $this->assertEquals(
        array('GÃ¼n'), 
        Pattern::compile('.Ã¼.', Pattern::UTF8)->match(new String('Günter', 'iso-8859-1'))->group(0)
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
        Pattern::compile('.ü.')->match(new String('Günter', 'iso-8859-1'))->group(0)
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
        Pattern::compile('a', Pattern::CASE_INSENSITIVE)->match('A')->length()
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
        Pattern::compile('H[ea]llo')->match('Hello')->groups()
      );
    }

    /**
     * Tests the MatchResult::groups() method
     *
     */
    #[@test]
    public function groupsWithOneMatch() {
      $this->assertEquals(
        array(array('www.example.com', 'www.', 'www', 'com')), 
        Pattern::compile('(([w]{3})\.)?example\.(com|net|org)')->match('www.example.com')->groups()
      );
    }

    /**
     * Tests the MatchResult::groups() method
     *
     */
    #[@test]
    public function groupsWithMultipleMatches() {
      $this->assertEquals(
        array(
          array('www.example.com', 'www.', 'www', 'com'),
          array('example.org', '', '', 'org'),
        ), 
        Pattern::compile('(([w]{3})\.)?example\.(com|net|org)')->match('www.example.com and example.org')->groups()
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
        Pattern::compile('H[ea]llo')->match('Hello')->group(0)
      );
    }

    /**
     * Tests the MatchResult::group() method
     *
     */
    #[@test]
    public function groupWithOneMatch() {
      $this->assertEquals(
        array('www.example.com', 'www.', 'www', 'com'), 
        Pattern::compile('(([w]{3})\.)?example\.(com|net|org)')->match('www.example.com')->group(0)
      );
    }

    /**
     * Tests the MatchResult::group() method
     *
     */
    #[@test]
    public function groupWithMultipleMatches() {
      $match= Pattern::compile('(([w]{3})\.)?example\.(com|net|org)')->match('www.example.com and example.org');
      $this->assertEquals(
        array('www.example.com', 'www.', 'www', 'com'), 
        $match->group(0)
      );
      $this->assertEquals(
        array('example.org', '', '', 'org'), 
        $match->group(1)
      );
    }

    /**
     * Tests the MatchResult::group() method
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function nonExistantGroup() {
      Pattern::compile('H[ea]llo')->match('Hello')->group(1);
    }

    /**
     * Tests the MatchResult::groups() method
     *
     */
    #[@test]
    public function matchEmptyString() {
      $this->assertEquals(array(), Pattern::compile('.')->match('')->groups());
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

    /**
     * Test Pattern::MULTILINE | Pattern::DOTALL example
     *
     */
    #[@test]
    public function multilineDotAll() {
      $m= Pattern::compile('BEGIN {(.+)}', Pattern::MULTILINE | Pattern::DOTALL)->match('BEGIN {
        print "Hello World";
      }');
      $this->assertEquals(1, $m->length());
      $group= $m->group(0);
      $this->assertEquals('print "Hello World";', trim($group[1]));
    }
  }
?>
