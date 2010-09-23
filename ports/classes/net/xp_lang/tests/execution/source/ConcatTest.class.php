<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests concatenation
   *
   */
  class net·xp_lang·tests·execution·source·ConcatTest extends ExecutionTest {
    
    /**
     * Test concatenating two strings
     *
     */
    #[@test]
    public function twoStrings() {
      $this->assertEquals(
        'HelloWorld', 
        $this->run('return "Hello" ~ "World";')
      );
    }

    /**
     * Test concatenating two variables
     *
     */
    #[@test]
    public function twoVariables() {
      $this->assertEquals(
        'HelloWorld', 
        $this->run('$a= "Hello"; $b= "World"; return $a ~ $b;')
      );
    }

    /**
     * Test concatenating a string to a variable
     *
     */
    #[@test]
    public function concatEqual() {
      $this->assertEquals(
        'HelloWorld', 
        $this->run('$a= "Hello"; $a ~= "World"; return $a;')
      );
    }

    /**
     * Test concatenating a string to a variable
     *
     */
    #[@test]
    public function bracedExpression() {
      $this->assertEquals(
        'Hello1World', 
        $this->run('$i= 0; return "Hello" ~ ($i + 1) ~ "World";')
      );
    }
  }
?>
