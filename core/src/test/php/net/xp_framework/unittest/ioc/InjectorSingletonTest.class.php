<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * Unittest
   */
  class InjectorSingletonTest extends TestCase {
    /**
     * Test using the SingletonScope
     */
    #[@test]
    public function withScope() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.TestNumber')
             ->to('net.xp_framework.unittest.ioc.helper.Random')
             ->asSingleton();

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.TestNumber'));

      $slot = $injector->getInstance('net.xp_framework.unittest.ioc.helper.SlotMachine');

      $this->assertInstanceOf('SlotMachine', $slot);
      $this->assertInstanceOf('TestNumber', $slot->number1);
      $this->assertInstanceOf('Random', $slot->number1);
      $this->assertInstanceOf('TestNumber', $slot->number2);
      $this->assertInstanceOf('Random', $slot->number2);
      $this->assertEquals($slot->number1, $slot->number2);
    }

    /**
     * Test the Singleton annotation
     */
    #[@test]
    public function withAnnotation() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.TestNumber')
             ->to('net.xp_framework.unittest.ioc.helper.RandomSingleton');

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.TestNumber'));

      $slot = $injector->getInstance('net.xp_framework.unittest.ioc.helper.SlotMachine');

      $this->assertInstanceOf('SlotMachine', $slot);
      $this->assertInstanceOf('TestNumber', $slot->number1);
      $this->assertInstanceOf('RandomSingleton', $slot->number1);
      $this->assertInstanceOf('TestNumber', $slot->number2);
      $this->assertInstanceOf('RandomSingleton', $slot->number2);
      $this->assertEquals($slot->number1, $slot->number2);
    }
  }
?>