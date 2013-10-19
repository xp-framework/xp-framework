<?php namespace net\xp_framework\unittest\remote;

use net\xp_framework\unittest\StartServer;
use unittest\TestCase;
use lang\Process;
use lang\Runtime;
use peer\Socket;
use lang\archive\Archive;
use remote\Remote;

/**
 * TestCase for Remote API
 *
 * @see      xp://remote.Remote
 */
#[@action(new StartServer('net.xp_framework.unittest.remote.TestingServer', 'connected', 'shutdown'))]
class IntegrationTest extends TestCase {
  protected static
    $bindAddress          = array(null, -1),
    $clientClassesLoader  = null;

  protected
    $remote= null;

  /**
   * Callback for when server is connected
   *
   * @param  string $bindAddress
   */
  public static function connected($bindAddress) {
    self::$bindAddress= explode(':', $bindAddress);
  }

  /**
   * Callback for when server should be shut down
   */
  public static function shutdown() {
    $s= new Socket(self::$bindAddress[0], self::$bindAddress[1]);
    $s->connect();
    $s->write(pack('Nc4Na*', DEFAULT_PROTOCOL_MAGIC_NUMBER, 1, 0, 61, false, 0, null));
    $s->close();
  }

  /**
   * Sets up test class
   */
  #[@beforeClass]
  public static function registerClientClasses() {
    $a= \lang\XPClass::forName(\xp::nameOf(__CLASS__))
      ->getPackage()
      ->getPackage('deploy')
      ->getResourceAsStream('beans.test.CalculatorBean.xar')
    ;
    self::$clientClassesLoader= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive($a)));
  }
  
  /**
   * Tears down test class
   */
  #[@afterClass]
  public static function removeClientClassLoader() {
    self::$clientClassesLoader && \lang\ClassLoader::removeLoader(self::$clientClassesLoader);
  }
  
  /**
   * Sets up this unittest
   */
  public function setUp() {
    try {
      $this->remote= Remote::forName('xp://'.self::$bindAddress[0].':'.self::$bindAddress[1]);
    } catch (\remote\RemoteException $e) {
      throw new \unittest\PrerequisitesNotMetError('Cannot setup client/server communication', $e);
    }
  }
  
  #[@test]
  public function lookup_calculator() {
    $calc= $this->remote->lookup('xp/test/Calculator');
    $this->assertSubclass($calc, 'beans.test.Calculator');
  }

  #[@test, @expect('remote.RemoteException')]
  public function lookup_non_existant() {
    $this->remote->lookup(':DOES_NOT_EXIST');
  }

  #[@test]
  public function call_add_method() {
    $this->assertEquals(3, $this->remote->lookup('xp/test/Calculator')->add(1, 2));
  }

  /**
   * Test calling a method
   *
   */
  #[@test, @ignore('Integers serialized to primitive ints')]
  public function addIntegersMethod() {
    $this->assertEquals(
      new \lang\types\Integer(3), 
      $this->remote->lookup('xp/test/Calculator')->addIntegers(new \lang\types\Integer(1), new \lang\types\Integer(2))
    );
  }

  /**
   * Test calling a method
   *
   */
  #[@test]
  public function addComplexNumbers() {
    $complex= self::$clientClassesLoader->loadClass('beans.test.Complex');
    $this->assertEquals(
      $complex->newInstance(5, 7), 
      $this->remote->lookup('xp/test/Calculator')->addComplexNumbers($complex->newInstance(2, 3), $complex->newInstance(3, 4))
    );
  }

  /**
   * Test calling a method with incorrect argument types raises
   * an IllegalArgumentException (this is done on the client-side
   * already)
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function addIntegersMethodWithIncorrectArguments() {
    $this->remote->lookup('xp/test/Calculator')->addIntegers(1, new \lang\types\Integer(2));
  }

  /**
   * Test calling a method
   *
   */
  #[@test, @expect(class = 'lang.Error', withMessage= '/Call to undefined method .+::doesNotExist()/')]
  public function callNonExistantMethod() {
    $this->remote->lookup('xp/test/Calculator')->doesNotExist();
  }
}
