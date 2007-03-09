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
     */
    #[@test]
    public function regularMember() {
      $this->assertSourcecodeEquals('$this->key;', $this->emit('$this->key;'));
    }

    /**
     * Tests dynamic member without {}
     *
     */
    #[@test]
    public function dynamicMember() {
      $this->assertSourcecodeEquals('$this->$key;', $this->emit('$this->$key;'));
    }

    /**
     * Tests dynamic member in {}
     *
     */
    #[@test]
    public function dynamicMemberInEvalBrackets() {
      $this->assertSourcecodeEquals('$this->{$key};', $this->emit('$this->{$key};'));
    }

    /**
     * Tests dynamic member in {}
     *
     */
    #[@test]
    public function dynamicMemberExpression() {
      $this->assertSourcecodeEquals('$this->{substr($key, 1)};', $this->emit('$this->{substr($key, 1)};'));
    }

    /**
     * Tests regular method
     *
     */
    #[@test]
    public function regularMethod() {
      $this->assertSourcecodeEquals('$this->key();', $this->emit('$this->key();'));
    }

    /**
     * Tests dynamic method without {}
     *
     */
    #[@test]
    public function dynamicMethod() {
      $this->assertSourcecodeEquals('$this->$key();', $this->emit('$this->$key();'));
    }

    /**
     * Tests dynamic method in {}
     *
     */
    #[@test]
    public function dynamicMethodInEvalBrackets() {
      $this->assertSourcecodeEquals('$this->{$key}();', $this->emit('$this->{$key}();'));
    }
  }
?>
