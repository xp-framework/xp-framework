<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.StringTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://lang.types.StringTokenizer
   * @purpose  TestCase
   */
  class StringTokenizerTest extends TestCase {
  
    /**
     * Test string tokenizing
     *
     */
    #[@test]
    public function testSimpleString() {
      $t= new StringTokenizer("Hello World!\nThis is an example", " \n");
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
     */
    #[@test]
    public function testSimpleStringWithDelims() {
      $t= new StringTokenizer("Hello World!\nThis is an example", " \n", TRUE);
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
     */
    #[@test]
    public function repetetiveDelimiters() {
      $t= new StringTokenizer("Hello \nWorld!", " \n");
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals('', $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
      $this->assertFalse($t->hasMoreTokens());
    }

    /**
     * Test string tokenizing
     *
     */
    #[@test]
    public function repetetiveDelimitersWithDelims() {
      $t= new StringTokenizer("Hello \nWorld!", " \n", TRUE);
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals("\n", $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
      $this->assertFalse($t->hasMoreTokens());
    }
  }
?>
