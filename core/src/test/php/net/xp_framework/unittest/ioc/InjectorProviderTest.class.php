<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'ioc.Binder',
    'net.xp_framework.unittest.ioc.helper.AnswerProvider'
  );

  /**
   * Unittest
   */
  class InjectorProviderTest extends TestCase {
    /**
     * use a provider for the injection
     */
    #[@test]
    public function injectWithProvider() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Answer')->toProvider(new AnswerProvider());
      $question = $binder->getInjector()->getInstance('net.xp_framework.unittest.ioc.helper.ExtendedQuestion');
      $this->assertInstanceOf('ExtendedQuestion', $question);
      $this->assertInstanceOf('Answer', $question->getAnswer());
    }

    /**
     * using an invalid provider class throws an exception
     */
    #[@test, @expect('ioc.BindingException')]
    public function injectWithInvalidProviderClassThrowsBinderException() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Answer')->toProviderClass('net.xp_framework.unittest.ioc.helper.Goodyear');
      $binder->getInjector()->getInstance('net.xp_framework.unittest.ioc.helper.ExtendedQuestion');
    }

    /**
     * injection with a provider class
     *
     * @test
     */
    public function injectWithProviderClass() {
      $binder = new Binder();
      $binder->bind('net.xp_framework.unittest.ioc.helper.Answer')->toProviderClass('net.xp_framework.unittest.ioc.helper.AnswerProvider');
      $question = $binder->getInjector()->getInstance('net.xp_framework.unittest.ioc.helper.ExtendedQuestion');
      $this->assertInstanceOf('ExtendedQuestion', $question);
      $this->assertInstanceOf('Answer', $question->getAnswer());
    }
  }
?>