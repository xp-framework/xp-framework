<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest', 'xp.compiler.checks.UninitializedVariables');

  /**
   * Tests variables
   *
   */
  class net·xp_lang·tests·execution·source·VariablesTest extends ExecutionTest {
  
    /**
     * Sets up this test. Add uninitialized variables check
     *
     */
    public function setUp() {
      parent::setUp();
      $this->check(new UninitializedVariables(), TRUE);
    }
    
    /**
     * Tests assigning to a regular variable
     *
     */
    #[@test]
    public function toVariable() {
      $this->assertEquals(1, $this->run('$a= 1; return $a;'));
    }

    /**
     * Tests assigning to a member variable
     *
     */
    #[@test]
    public function toMember() {
      $this->assertEquals(1, $this->run('$this.member= 1; return $this.member;'));
    }
    
    /**
     * Tests $a= $b= 1;
     *
     */
    #[@test]
    public function duplicate() {
      $this->assertEquals(array(1, 1), $this->run('$a= $b= 1; return [$a, $b];'));
    }

    /**
     * Tests $a= $b= $c= 1;
     *
     */
    #[@test]
    public function triple() {
      $this->check(new UninitializedVariables(), TRUE);
      $this->assertEquals(array(1, 1, 1), $this->run('$a= $b= $c= 1; return [$a, $b, $c];'));
    }

    /**
     * Tests $a++; where $a is undefined
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function uninitialized() {
      $this->run('$a++;');
    }
  }
?>
