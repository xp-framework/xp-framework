<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.migrate.AbstractRewriterTest');

  /**
   * Tests is_a() -> instanceof rewriting
   *
   * @purpose  Unit Test
   */
  class InstanceOfTest extends AbstractRewriterTest {

    /**
     * Tests is_a() when used with a variable and a string
     *
     * @access  public
     */
    #[@test]
    function variableAndString() {
      $this->assertExpressionRewritten('($a instanceof util.Date);', 'is_a($a, "Date");');
    }

    /**
     * Tests is_a() when used with a variable and a variable
     *
     * @access  public
     */
    #[@test]
    function variableAndVariable() {
      $this->assertExpressionRewritten('($a instanceof $class);', 'is_a($a, $class);');
    }

    /**
     * Tests is_a() when used with an array access and a string
     *
     * @access  public
     */
    #[@test]
    function arrayAccessAndString() {
      $this->assertExpressionRewritten('($a[$i] instanceof util.Date);', 'is_a($a[$i], "Date");') &&
      $this->assertExpressionRewritten('($a[0] instanceof util.Date);', 'is_a($a[0], "Date");');
    }

    /**
     * Tests is_a() when used with a member variable and a string
     *
     * @access  public
     */
    #[@test]
    function memberVariableAndString() {
      $this->assertExpressionRewritten('($this->date instanceof util.Date);', 'is_a($this->date, "Date");');
    }

    /**
     * Tests is_a() when used with a dynamic member variable and a string
     *
     * @access  public
     */
    #[@test]
    function dynamicMemberVariableAndString() {
      $this->assertExpressionRewritten('($this->$key instanceof util.Date);', 'is_a($this->$key, "Date");') &&
      $this->assertExpressionRewritten('($this->{$key} instanceof util.Date);', 'is_a($this->{$key}, "Date");');
    }

    /**
     * Tests is_a() when used with a member variable and a string
     *
     * @access  public
     */
    #[@test]
    function arrayAccessOnMemberVariableAndString() {
      $this->assertExpressionRewritten('($this->dates[0] instanceof util.Date);', 'is_a($this->dates[0], "Date");') &&
      $this->assertExpressionRewritten('($this->dates[$i] instanceof util.Date);', 'is_a($this->dates[$i], "Date");');
    }

    /**
     * Tests is_a() when used with a expression and a string
     *
     * @access  public
     */
    #[@test]
    function expressionAndString() {
      $this->assertExpressionRewritten('(get_class($this) instanceof util.Date);', 'is_a(get_class($this), "Date");');
    }
  }
?>
