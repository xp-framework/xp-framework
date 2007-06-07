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
     */
    #[@test]
    public function interfaceWithoutPackage() {
      $this->assertSourcecodeEquals(
        'interface main·Traceable{};',
        $this->emit('interface Traceable { }')
      );
    }

    /**
     * Tests emitting an interface within a package
     *
     */
    #[@test]
    public function interfaceInPackage() {
      $this->assertSourcecodeEquals(
        '$package= \'de.thekid\'; interface de·thekid·Traceable{};',
        $this->emit('package de.thekid { interface Traceable { } }')
      );
    }

    /**
     * Tests interface declarations are omitted (checked at compile time)
     *
     */
    #[@test]
    public function interfaceDeclarationsAreOmitted() {
      $this->assertSourcecodeEquals(
        '$package= \'de.thekid\'; interface de·thekid·Traceable{};',
        $this->emit('package de.thekid { interface Traceable { public void setTrace($cat); } }')
      );
    }

    /**
     * Tests emitting an interface with a parent
     *
     */
    #[@test]
    public function interfaceWithParent() {
      $this->assertSourcecodeEquals(
        'interface main·Base{}; interface main·Child extends main·Base{};',
        $this->emit('interface Base { } interface Child extends Base { }')
      );
    }

    /**
     * Tests emitting an interface with more than one parent
     *
     */
    #[@test]
    public function interfaceWithParents() {
      $this->assertSourcecodeEquals(
        'interface main·Base{}; interface main·Being{}; interface main·Child extends main·Base, main·Being{};',
        $this->emit('interface Base { } interface Being { } interface Child extends Base, Being { }')
      );
    }

    /**
     * Tests emitting an interface with non-existant parent
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function interfaceWithNonExistantParent() {
      $this->emit('interface Child extends Base { }');
    }
  }
?>
