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
  class ClassEmitterTest extends AbstractEmitterTest {

    /**
     * Tests emitting a class outside of a package
     *
     * @access  public
     */
    #[@test]
    function classWithoutPackage() {
      $this->assertSourcecodeEquals(
        'class main·Test extends lang·Object{};',
        $this->emit('class Test { }')
      );
    }

    /**
     * Tests emitting a class within a package
     *
     * @access  public
     */
    #[@test]
    function classInPackage() {
      $this->assertSourcecodeEquals(
        'class de·thekid·Test extends lang·Object{};',
        $this->emit('package de.thekid { class Test { } }')
      );
    }

    /**
     * Tests emitting a class with a parent class
     *
     * @access  public
     */
    #[@test]
    function classWithParent() {
      $this->assertSourcecodeEquals(
        'class main·Base extends lang·Object{}; class main·Test extends main·Base{};',
        $this->emit('class Base { } class Test extends Base { }')
      );
    }

    /**
     * Tests emitting a class with a parent class
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function classWithNonExistantParent() {
      $this->emit('class Test extends Base { }');
    }

    /**
     * Tests interfaces are implemented correctly
     *
     * @access  public
     */
    #[@test]
    function implementedInterface() {
      $this->assertSourcecodeEquals(
        'interface main·A{}; class main·Test extends lang·Object implements main·A{public function a(){echo \'A\'; }};',
        $this->emit('interface A { public void a(); } class Test implements A { public void a() { echo \'A\'; } }')
      );
    }

    /**
     * Tests interfaces are implemented correctly
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function failingToImplementInterface() {
      $this->emit('interface A { public void a(); } class Test implements A { }');
    }

    /**
     * Tests emitting a class that implements an interface
     *
     * @access  public
     */
    #[@test]
    function classImplementingInterface() {
      $this->assertSourcecodeEquals(
        'interface main·Traceable{}; class main·Test extends lang·Object implements main·Traceable{};',
        $this->emit('interface Traceable { } class Test implements Traceable { }')
      );
    }

    /**
     * Tests emitting a class that implements a non-existant interface
     *
     * @access  public
     */
    #[@test, @expect('lang.FormatException')]
    function classImplementingNonExistantInterface() {
      $this->emit('class main·Test implements Traceable { }');
    }

    /**
     * Tests emitting a class that implements more than one interface
     *
     * @access  public
     */
    #[@test]
    function classImplementingInterfaces() {
      $this->assertSourcecodeEquals(
        'interface main·A{}; interface main·B{}; class main·Test extends lang·Object implements main·A, main·B{};',
        $this->emit('interface A { } interface B { } class Test implements A, B { }')
      );
    }
  }
?>
