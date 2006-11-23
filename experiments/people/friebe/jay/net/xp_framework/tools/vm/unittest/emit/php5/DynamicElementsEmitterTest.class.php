<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class DynamicElementsEmitterTest extends AbstractEmitterTest {

    /**
     * Tests regular member
     *
     * @access  public
     */
    #[@test]
    function regularMember() {
      $this->assertSourcecodeEquals('$this->key;', $this->emit('$this->key;'));
    }

    /**
     * Tests dynamic member without {}
     *
     * @access  public
     */
    #[@test]
    function dynamicMember() {
      $this->assertSourcecodeEquals('$this->$key;', $this->emit('$this->$key;'));
    }

    /**
     * Tests dynamic member in {}
     *
     * @access  public
     */
    #[@test]
    function dynamicMemberInEvalBrackets() {
      $this->assertSourcecodeEquals('$this->{$key};', $this->emit('$this->{$key};'));
    }

    /**
     * Tests dynamic member in {}
     *
     * @access  public
     */
    #[@test]
    function dynamicMemberExpression() {
      $this->assertSourcecodeEquals('$this->{substr($key, 1)};', $this->emit('$this->{substr($key, 1)};'));
    }

    /**
     * Tests regular method
     *
     * @access  public
     */
    #[@test]
    function regularMethod() {
      $this->assertSourcecodeEquals('$this->key();', $this->emit('$this->key();'));
    }

    /**
     * Tests dynamic method without {}
     *
     * @access  public
     */
    #[@test]
    function dynamicMethod() {
      $this->assertSourcecodeEquals('$this->$key();', $this->emit('$this->$key();'));
    }

    /**
     * Tests dynamic method in {}
     *
     * @access  public
     */
    #[@test]
    function dynamicMethodInEvalBrackets() {
      $this->assertSourcecodeEquals('$this->{$key}();', $this->emit('$this->{$key}();'));
    }
  }
?>
