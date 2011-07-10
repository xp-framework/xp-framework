<?php
/* This class is part of the XP framework
 *
 * $Id: InjectorConstantTest.class.php 2995 2011-02-13 09:32:05Z mikey $
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * Unittest
   */
  class InjectorNamedTest extends TestCase {
    /**
     * name based setter injection with single param
     */
    #[@test]
    public function namedSetterInjectionWithSingleParam() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Person')->named('schst')->to('net.xp_framework.unittest.ioc.helper.Schst');
      $binder->bind('net.xp_framework.unittest.ioc.helper.Person')->to('net.xp_framework.unittest.ioc.helper.Mikey');

      $injector = $binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Person', 'schst'));
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.Person'));

      $group = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Developers');

      $this->assertInstanceOf('Developers', $group);
      $this->assertInstanceOf('Person', $group->mikey);
      $this->assertInstanceOf('Mikey', $group->mikey);
      $this->assertInstanceOf('Person', $group->schst);
      $this->assertInstanceOf('Schst', $group->schst);
    }
  }
?>