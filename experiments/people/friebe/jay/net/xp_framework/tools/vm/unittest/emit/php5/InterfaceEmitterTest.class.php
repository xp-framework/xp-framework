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
  class InterfaceEmitterTest extends AbstractEmitterTest {

    /**
     * Tests emitting an interface outside of a package
     *
     * @access  public
     */
    #[@test]
    function interfaceWithoutPackage() {
      $this->assertSourcecodeEquals(
        'interface Traceable{};',
        $this->emit('interface Traceable { }')
      );
    }

    /**
     * Tests emitting an interface within a package
     *
     * @access  public
     */
    #[@test]
    function interfaceInPackage() {
      $this->assertSourcecodeEquals(
        'interface de·thekid·Traceable{};',
        $this->emit('package de~thekid { interface Traceable { } }')
      );
    }

    /**
     * Tests interface declarations are omitted (checked at compile time)
     *
     * @access  public
     */
    #[@test]
    function interfaceDeclarationsAreOmitted() {
      $this->assertSourcecodeEquals(
        'interface de·thekid·Traceable{};',
        $this->emit('package de~thekid { interface Traceable { public void setTrace($cat); } }')
      );
    }

    /**
     * Tests emitting an interface with a parent
     *
     * @access  public
     */
    #[@test]
    function interfaceWithParent() {
      $this->assertSourcecodeEquals(
        'interface Base{}; interface Child extends Base{};',
        $this->emit('interface Base { } interface Child extends Base { }')
      );
    }

    /**
     * Tests emitting an interface with more than one parent
     *
     * @access  public
     */
    #[@test]
    function interfaceWithParents() {
      $this->assertSourcecodeEquals(
        'interface Base{}; interface Being{}; interface Child extends Base, Being{};',
        $this->emit('interface Base { } interface Being { } interface Child extends Base, Being { }')
      );
    }

    /**
     * Tests emitting an interface with non-existant parent
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function interfaceWithNonExistantParent() {
      $this->emit('interface Child extends Base { }');
    }
  }
?>
