<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.arguments.PatternMatcher',
    'unittest.TestCase'
  );

  /**
   * Testcase for PatternMatcher class
   *
   * @see   xp://unittest.mock.arguments.PatternMatcher
   */
  class PatternMatcherTest extends TestCase {

    /**
     * Test construction
     */
    #[@test]
    public function construction_should_work_with_string_parameter() {
      new PatternMatcher('foobar');
    }

    /**
     * Test prefix matching
     */
    #[@test]
    public function prefix_match_test() {
      $matcher= new PatternMatcher('/^foo/');
      $this->assertTrue($matcher->matches('foooo'));
      $this->assertTrue($matcher->matches('foo'));
      $this->assertTrue($matcher->matches('foo '));
      $this->assertTrue($matcher->matches('foo asdfa'));
      $this->assertFalse($matcher->matches('xfoo'));
      $this->assertFalse($matcher->matches(' foo '));
    }

    /**
     * Test exact matching
     */
    #[@test]
    public function exact_match_test() {
      $matcher= new PatternMatcher('/^foo$/');
      $this->assertTrue($matcher->matches('foo'));
      $this->assertFalse($matcher->matches('foooo'));
      $this->assertFalse($matcher->matches('foo '));
      $this->assertFalse($matcher->matches('foo asdfa'));
      $this->assertFalse($matcher->matches('xfoox'));
      $this->assertFalse($matcher->matches(' foo '));
    }

    /**
     * Test pattern matching
     */
    #[@test]
    public function pattern_match_test() {
      $matcher= new PatternMatcher('/fo+o.*/');
      $this->assertTrue($matcher->matches('foooo'));
      $this->assertTrue($matcher->matches('fooooooooo'));
      $this->assertTrue($matcher->matches('adsfafdsfooooooooo'));
      $this->assertTrue($matcher->matches('asdfaf fooo dsfasfd'));
      $this->assertFalse($matcher->matches('fobo'));
      $this->assertFalse($matcher->matches('fo'));
    }
  }
?>