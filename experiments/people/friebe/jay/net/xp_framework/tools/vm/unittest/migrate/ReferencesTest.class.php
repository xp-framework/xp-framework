<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.migrate.AbstractRewriterTest');

  /**
   * Tests references are removed
   *
   * @purpose  Unit Test
   */
  class ReferencesTest extends AbstractRewriterTest {

    /**
     * Tests reference is removed before &new 
     *
     * @access  public
     */
    #[@test]
    function removedFromNew() {
      $this->assertExpressionRewritten('$now= new util.Date();', '$now= &new Date();');
    }

    /**
     * Tests reference is removed from static method call
     *
     * @access  public
     */
    #[@test]
    function removedFromStaticMethodCall() {
      $this->assertExpressionRewritten('$l= util.Date::create();', '$l= &Date::create();');
    }

    /**
     * Tests reference is removed from clone call
     *
     * @access  public
     */
    #[@test]
    function removedFromCloneCall() {
      $this->assertExpressionRewritten('$c= clone($o);', '$c= &clone($o);');
    }

    /**
     * Tests reference is removed in assignments 
     *
     * @access  public
     */
    #[@test]
    function removedFromAssignment() {
      $this->assertExpressionRewritten('$a= $b;', '$a= &$b;');
    }

    /**
     * Tests reference is removed in arrays 
     *
     * @access  public
     */
    #[@test]
    function removedFromArrays() {
      $this->assertExpressionRewritten('$a= array($b);', '$a= array(&$b);');
    }

    /**
     * Tests reference is removed in arrays 
     *
     * @access  public
     */
    #[@test]
    function removedFromMethodArguments() {
      $this->assertMethodRewritten('public void equals($cmp) { }', 'equals', array(), '(&$cmp) { }');
    }
  }
?>
