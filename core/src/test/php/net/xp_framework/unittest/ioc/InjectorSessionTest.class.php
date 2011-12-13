<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'ioc.Binder',
    'ioc.SessionBindingScope',
    'net.xp_framework.unittest.ioc.helper.DummySession'
  );

  /**
   * Unittest
   */
  class InjectorSessionTest extends TestCase {
    protected
      $binder,
      $dummySession;

    /**
     * set up test environment
     */
    public function setUp() {
      $this->binder       = new Binder();
      $this->dummySession = new DummySession();
      $this->binder->setSessionScope(new SessionBindingScope($this->dummySession));
    }

    /**
     * stores created instance in session
     */
    #[@test]
    public function storesCreatedInstanceInSession() {
      $this->binder->bind('net.xp_framework.unittest.ioc.helper.TestNumber')
                   ->to('net.xp_framework.unittest.ioc.helper.Random')
                   ->inSession();
      $injector = $this->binder->getInjector();

      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.TestNumber'));

      $number = $injector->getInstance('net.xp_framework.unittest.ioc.helper.TestNumber');
      $this->assertInstanceOf('TestNumber', $number);
      $this->assertInstanceOf('Random', $number);
      $this->assertTrue($this->dummySession->hasValue(SessionBindingScope::SESSION_KEY . 'net.xp_framework.unittest.ioc.helper.Random'));
      $this->assertEquals($number,
                          $this->dummySession->getValue(SessionBindingScope::SESSION_KEY . 'net.xp_framework.unittest.ioc.helper.Random')
      );
      $this->assertEquals($number,
                          $injector->getInstance('net.xp_framework.unittest.ioc.helper.TestNumber')
      );
    }

    /**
     * uses instance from session if available
     */
    #[@test]
    public function usesInstanceFromSessionIfAvailable() {
      $this->binder->bind('net.xp_framework.unittest.ioc.helper.TestNumber')
                   ->to('net.xp_framework.unittest.ioc.helper.Random')
                   ->inSession();
      $injector = $this->binder->getInjector();
      $number   = new Random();
      $this->dummySession->putValue(SessionBindingScope::SESSION_KEY . 'net.xp_framework.unittest.ioc.helper.Random', $number);
      $this->assertTrue($injector->hasBinding('net.xp_framework.unittest.ioc.helper.TestNumber'));
      $this->assertEquals($number,
                          $injector->getInstance('net.xp_framework.unittest.ioc.helper.TestNumber')
      );
    }

    /**
     * no session available throws RuntimeError
     */
    #[@test, @expect('lang.RuntimeError')]
    public function bindingInSessionScopeWithoutAvailableSessionThrowsRuntimeError()
    {
        $this->binder = new Binder();
        $this->binder->bind('net.xp_framework.unittest.ioc.helper.TestNumber')
                     ->to('net.xp_framework.unittest.ioc.helper.Random')
                     ->inSession();
    }
  }
?>