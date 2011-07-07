<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'ioc.App',
    'net.xp_framework.unittest.ioc.helper.AppTestBindingModuleOne'
  );

  /**
   * @purpose  Unittest
   */
  class AppTest extends TestCase {

    /**
     * invalid binding module class throws illegal argument exception
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function invalidBindingModuleClassThrowsIllegalArgumentException() {
      App::createInjector(newinstance('Object', array(), '{}')->getClassName());
    }

    /**
     * invalid binding module instance throws illegal argument exception
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException() {
      App::createInjector(newinstance('Object', array(), '{}'));
    }

    /**
     * all binding modules should be processed
     */
    #[@test]
    public function bindingModulesAreProcessed() {
      $injector = App::createInjector(new AppTestBindingModuleOne(),
                                      'net.xp_framework.unittest.ioc.helper.AppTestBindingModuleTwo'
                  );
      $this->assertTrue($injector->hasBinding('lang.Object'));
      $this->assertTrue($injector->hasBinding('lang.Generic'));
      $this->assertTrue($injector->hasBinding('ioc.Injector'));
      $this->assertEquals($injector, $injector->getInstance('ioc.Injector'));
    }

    /**
     * binding modules are processed if passed as array
     */
    #[@test]
    public function bindingModulesAreProcessedIfPassedAsArray() {
      $injector = App::createInjector(array(new AppTestBindingModuleOne(),
                                            'net.xp_framework.unittest.ioc.helper.AppTestBindingModuleTwo'
                                      )
                  );
      $this->assertTrue($injector->hasBinding('lang.Object'));
      $this->assertTrue($injector->hasBinding('lang.Generic'));
      $this->assertTrue($injector->hasBinding('ioc.Injector'));
      $this->assertEquals($injector, $injector->getInstance('ioc.Injector'));
    }

    /**
     * createInstance() creates an instance using bindings
     */
    #[@test]
    public function createInstanceCreatesInstanceUsingBindings() {
      $app = App::createInstance('net.xp_framework.unittest.ioc.helper.AppWithBindingsAndArguments');
      $this->assertInstanceOf('net.xp_framework.unittest.ioc.helper.AppWithBindingsAndArguments', $app);
      $this->assertInstanceOf('lang.Object', $app->getObject());
      $this->assertEquals('foo', $app->getObject()->get());
      $this->assertInstanceOf('lang.Object', $app->getGeneric());
      $this->assertEquals('bar', $app->getGeneric()->get());
    }

    /**
     * createInstance() creates an instance without bindings
     */
    #[@test]
    public function createInstanceCreatesInstanceWithoutBindings() {
      $this->assertInstanceOf('net.xp_framework.unittest.ioc.helper.AppTestBindingModuleTwo',
                              App::createInstance('net.xp_framework.unittest.ioc.helper.AppTestBindingModuleTwo')
      );
    }

    /**
     * createInstance() creates an instance with arguments
     */
    #[@test]
    public function createInstanceWithArguments() {
      $appClassWithArgument = App::createInstance('net.xp_framework.unittest.ioc.helper.AppWithBindingsAndArguments',  array('baz'));
      $this->assertInstanceOf('net.xp_framework.unittest.ioc.helper.AppWithBindingsAndArguments', $appClassWithArgument);
      $this->assertEquals('baz', $appClassWithArgument->getArgument());
    }
  }
?>