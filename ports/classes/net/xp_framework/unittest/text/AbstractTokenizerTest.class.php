<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.Tokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.Tokenizer
   * @purpose  Abstract base class for different tokenizer tests
   */
  abstract class AbstractTokenizerTest extends TestCase {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected abstract function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE);
  
    /**
     * Test string tokenizing
     *
     */
    #[@test]
    public function testSimpleString() {
      $t= $this->tokenizerInstance("Hello World!\nThis is an example", " \n");
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
      $t= $this->tokenizerInstance("Hello World!\nThis is an example", " \n", TRUE);
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
      $t= $this->tokenizerInstance("Hello \nWorld!", " \n");
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
      $t= $this->tokenizerInstance("Hello \nWorld!", " \n", TRUE);
      $this->assertEquals('Hello', $t->nextToken());
      $this->assertEquals(' ', $t->nextToken());
      $this->assertEquals("\n", $t->nextToken());
      $this->assertEquals('World!', $t->nextToken());
      $this->assertFalse($t->hasMoreTokens());
    }
    
    /**
     * Test for loop iteration
     *
     */
    #[@test]
    public function forIteration() {
      $r= array();
      for ($t= $this->tokenizerInstance('A B C', ' '); $t->hasMoreTokens(); ) {
        $r[]= $t->nextToken();
      }
      $this->assertEquals(range('A', 'C'), $r);
    }

    /**
     * Test while loop iteration
     *
     */
    #[@test]
    public function whileIteration() {
      $r= array();
      $t= $this->tokenizerInstance('A B C', ' ');
      while ($t->hasMoreTokens()) {
        $r[]= $t->nextToken();
      }
      $this->assertEquals(range('A', 'C'), $r);
    }

    /**
     * Test foreach() overloading
     *
     */
    #[@test]
    public function foreachIteration() {
      $r= array();
      foreach ($this->tokenizerInstance('A B C', ' ') as $token) {
        $r[]= $token;
      }
      $this->assertEquals(range('A', 'C'), $r);
    }

    /**
     * Test resetting a tokenizer
     *
     */
    #[@test]
    public function reset() {
      $t= $this->tokenizerInstance('A B C', ' ');
      $this->assertTrue($t->hasMoreTokens());
      $this->assertEquals('A', $t->nextToken());
      $t->reset();
      $this->assertTrue($t->hasMoreTokens());
      $this->assertEquals('A', $t->nextToken());
    }
  }
?>
