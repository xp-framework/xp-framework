<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'ioc.Binder',
    'ioc.Injector',
    'ioc.module.ArgumentsBindingModule'
  );

  /**
   * @purpose  Unittest
   */
  class ArgumentsBindingModuleTest extends TestCase {

    /**
     * given list of arguments are bound
     */
    #[@test]
    public function argumentsAreBound()
    {
      $injector               = new Injector();
      $argumentsBindingModule = new ArgumentsBindingModule(array('foo', 'bar', 'baz'));
      $argumentsBindingModule->configure(new Binder($injector));
      $this->assertTrue($injector->hasConstant('argv.0'));
      $this->assertTrue($injector->hasConstant('argv.1'));
      $this->assertTrue($injector->hasConstant('argv.2'));
      $this->assertEquals('foo', $injector->getConstant('argv.0'));
      $this->assertEquals('bar', $injector->getConstant('argv.1'));
      $this->assertEquals('baz', $injector->getConstant('argv.2'));
    }
  }
?>