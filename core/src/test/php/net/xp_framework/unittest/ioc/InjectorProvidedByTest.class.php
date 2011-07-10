<?php
/* This class is part of the XP framework
 *
 * $Id: InjectorConstantTest.class.php 2995 2011-02-13 09:32:05Z mikey $
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * Unittest
   */
  class InjectorProvidedByTest extends TestCase {
    /**
     * annotation based provider
     */
    #[@test]
    public function annotatedProvider() {
      $binder   = new Binder();
      $injector = $binder->getInjector();
      $person   = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Developer');
      $this->assertInstanceOf('Schst', $person);
    }

    /**
     * override annotation based provider with explicit binding
     */
    #[@test]
    public function overrideAnnotationProviderWithExplicitBinding() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Developer')->to('net.xp_framework.unittest.ioc.helper.Mikey');
      $injector = $binder->getInjector();
      $person   = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Developer');
      $this->assertInstanceOf('Mikey', $person);
    }
  }
?>