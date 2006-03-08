<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'text.StringTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.StringTokenizer
   * @purpose  TestCase
   */
  class StringTokenizerTest extends TestCase {
  
    /**
     * Test string tokenizing
     *
     * @access  public
     */
    #[@test]
    function testSimpleString() {
      $t= &new StringTokenizer("Hello World!\nThis is an example", " \n");
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
      $this->assertEquals('This', $t->nextToken());
      $this->assertEquals('is', $t->nextToken());
      $this->assertEquals('an', $t->nextToken());
      $this->assertEquals('example', $t->nextToken());
      $this->assertFalse($t->hasMoreTokens());
    }

    /**
     * Test string tokenizing
     *
     * @access  public
     */
    #[@test]
    function testSimpleStringWithDelims() {
      $t= &new StringTokenizer("Hello World!\nThis is an example", " \n", TRUE);
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
      $this->assertEquals("\n", $t->nextToken());
      $this->assertEquals('This', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals('is', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals('an', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals('example', $t->nextToken());
      $this->assertFalse($t->hasMoreTokens());
    }
    
    /**
     * Test string tokenizing
     *
     * @access  public
     */
    #[@test]
    function repetetiveDelimiters() {
      $t= &new StringTokenizer("Hello \nWorld!", " \n", TRUE);
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals("\n", $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
    }
  }
?>
