<?php
/* This class is part of the XP framework
 *
 * $Id: InjectorConstantTest.class.php 3081 2011-03-01 22:11:00Z mikey $
 */

  uses('unittest.TestCase', 'ioc.Binder');

  /**
   * @purpose  Unittest
   */
  class InjectorConstantTest extends TestCase {
    /**
     * Test a constant injection
     */
    #[@test]
    public function injectConstant() {
      $binder = new Binder();
      $binder->bindConstant()->named('answer')->to(42);
      $injector = $binder->getInjector();
      $question = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Question');
      $this->assertInstanceOf('Question', $question);
      $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * checking for a non-existent constant binding should return FALSE
     */
    #[@test]
    public function checkForNonExistingConstantBindingShouldReturnFalse() {
      $injector = new Injector();
      $this->assertFalse($injector->hasBinding(ConstantBinding::TYPE, 'test'));
    }

    /**
     * test shortcut for retrieving constant values
     */
    #[@test]
    public function shortcutForRetrievingConstantValues() {
      $binder = new Binder();
      $injector = $binder->getInjector();
      $this->assertFalse($injector->hasConstant('answer'));
      $binder->bindConstant()->named('answer')->to(42);
      $this->assertTrue($injector->hasConstant('answer'));
      $this->assertEquals(42, $injector->getConstant('answer'));
    }

    /**
     * test injection provider instance
     */
    #[@test]
    public function constantViaInjectionProviderInstance()
    {
      $binder = new Binder();
      $binder->bindConstant()
             ->named('answer')
             ->toProvider(new ValueInjectionProvider(42));
      $injector = $binder->getInjector();
      $this->assertTrue($injector->hasConstant('answer'));
      $this->assertEquals(42, $injector->getConstant('answer'));
      $question = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Question');
      $this->assertInstanceOf('Question', $question);
      $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * test that an invalid injection provider class throws a binding exception
     */
    #[@test, @expect('ioc.BindingException')]
    public function constantViaInvalidInjectionProviderClassThrowsBindingException()
    {
      $binder = new Binder();
      $binder->bindConstant()
             ->named('answer')
             ->toProviderClass('net.xp_framework.unittest.ioc.helper.Mikey');
      $binder->getInjector()->getConstant('answer');
    }

    /**
     * test constant binding with injection provider class
     */
    #[@test]
    public function constantViaInjectionProviderClass()
    {
      $binder = new Binder();
      $binder->bindConstant()
             ->named('answer')
             ->toProviderClass(XPClass::forName('net.xp_framework.unittest.ioc.helper.InjectorAnswerConstantProvider'));
      $injector = $binder->getInjector();
      $this->assertTrue($injector->hasConstant('answer'));
      $this->assertEquals(42, $injector->getConstant('answer'));
      $question = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Question');
      $this->assertInstanceOf('Question', $question);
      $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * test constant binding with injection provider class name
     */
    #[@test]
    public function constantViaInjectionProviderClassName()
    {
        $binder = new Binder();
        $binder->bindConstant()
               ->named('answer')
               ->toProviderClass('net.xp_framework.unittest.ioc.helper.InjectorAnswerConstantProvider');
        $injector = $binder->getInjector();
        $this->assertTrue($injector->hasConstant('answer'));
        $this->assertEquals(42, $injector->getConstant('answer'));
        $question = $injector->getInstance('net.xp_framework.unittest.ioc.helper.Question');
        $this->assertInstanceOf('Question', $question);
        $this->assertEquals(42, $question->getAnswer());
    }
  }
?>