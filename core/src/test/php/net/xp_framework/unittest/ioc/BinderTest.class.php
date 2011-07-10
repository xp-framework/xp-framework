<?php
/* This class is part of the XP framework
 *
 * $Id: BinderTest.class.php 2991 2011-02-12 23:35:48Z mikey $
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * Unittest
   */
  class BinderTest extends TestCase {

    /**
     * binder returns an injector
     */
    #[@test]
    public function binderReturnsInjector()
    {
      $binder   = new Binder();
      $injector = $binder->getInjector();
      $this->assertInstanceOf('Injector', $injector);
    }

    /**
     * binder always returns same injector
     */
    #[@test]
    public function sameInjector()
    {
      $binder    = new Binder();
      $injector  = $binder->getInjector();
      $injector2 = $binder->getInjector();
      $this->assertEquals(spl_object_hash($injector), spl_object_hash($injector2));
    }

    /**
     * given injector should be used instead of creating a new one
     */
    #[@test]
    public function injectedInjectorIsUsed()
    {
        $injector = new Injector();
        $binder   = new Binder($injector);
        $this->assertEquals(spl_object_hash($injector), spl_object_hash($binder->getInjector()));
    }
  }
?>