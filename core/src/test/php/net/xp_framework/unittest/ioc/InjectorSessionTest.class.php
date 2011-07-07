<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'ioc.Binder',
    'net.xp_framework.unittest.ioc.helper.DummySession'
  );

  /**
   * @purpose  Unittest
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
      $this->binder->setSessionForSessionScope($this->dummySession);
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
      $this->assertEquals(spl_object_hash($number),
                          spl_object_hash($this->dummySession->getValue(SessionBindingScope::SESSION_KEY . 'net.xp_framework.unittest.ioc.helper.Random'))
      );
      $this->assertEquals(spl_object_hash($number),
                          spl_object_hash($injector->getInstance('net.xp_framework.unittest.ioc.helper.TestNumber'))
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
      $this->assertEquals(spl_object_hash($number),
                          spl_object_hash($injector->getInstance('net.xp_framework.unittest.ioc.helper.TestNumber'))
      );
    }

    /**
     * no session available throws RuntimeError
     */
    #[@test, @expect('lang.RuntimeError')]
    public function noSessionAvailableThrowsRuntimeError()
    {
        $scope = new SessionBindingScope();
        $scope->getInstance(XPClass::forName('net.xp_framework.unittest.ioc.helper.TestNumber'),
                            XPClass::forName('net.xp_framework.unittest.ioc.helper.Random'),
                            newinstance('ioc.InjectionProvider', array(), '{ public function get($name = null) { } }')
        );
    }
  }
?>