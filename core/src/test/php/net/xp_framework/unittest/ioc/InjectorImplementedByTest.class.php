<?php
/* This class is part of the XP framework
 *
 * $Id: InjectorImplementedByTest.class.php 2996 2011-02-13 09:33:11Z mikey $
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * @purpose  Unittest
   */
  class InjectorImplementedByTest extends TestCase {
    /**
     * Test the default binding
     */
    #[@test]
    public function defaultImplementation() {
      $binder   = new Binder();
      $injector = $binder->getInjector();
      $person   = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Person');
      $this->assertInstanceOf('Schst', $person);
    }

    /**
     * Test overriding the default binding
     */
    #[@test]
    public function override() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Person')->to('net.xp_framework.unittest.ioc.helper.Mikey');
      $injector = $binder->getInjector();
      $person   = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Person');
      $this->assertInstanceOf('Mikey', $person);
    }
  }
?>