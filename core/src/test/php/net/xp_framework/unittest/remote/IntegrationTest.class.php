<?php namespace net\xp_framework\unittest\remote;



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
 * @purpose  Unittest
 */
class IntegrationTest extends TestCase {
  protected static
    $serverProcess        = null,
    $bindAddress          = array(null, -1),
    $clientClassesLoader  = null;

  protected
    $remote= null;

  /**
   * Sets up test case
   *
   */
  #[@beforeClass]
  public static function startApplicationServer() {

    // Arguments to server process
    $args= array(
      'debugServerProtocolToFile' => null,   
    );

    // Start server process
    self::$serverProcess= Runtime::getInstance()->newInstance(
      null, 
      'class', 
      'net.xp_framework.unittest.remote.TestingServer',
      array_values($args)
    );
    self::$serverProcess->in->close();

    // Check if startup succeeded
    $status= self::$serverProcess->out->readLine();
    if (2 != sscanf($status, '+ Service %[0-9.]:%d', self::$bindAddress[0], self::$bindAddress[1])) {
      try {
        self::shutdownApplicationServer();
      } catch (\lang\IllegalStateException $e) {
        $status.= $e->getMessage();
      }
      throw new \unittest\PrerequisitesNotMetError('Cannot start EASC server: '.$status, null);
    }

    // Add classloader with CalculatorBean client classes
    $a= \lang\XPClass::forName(\xp::nameOf(__CLASS__))
      ->getPackage()
      ->getPackage('deploy')
      ->getResourceAsStream('beans.test.CalculatorBean.xar')
    ;
    self::$clientClassesLoader= \lang\ClassLoader::registerLoader(new \lang\archive\ArchiveClassLoader(new Archive($a)));
  }
  
  /**
   * Shut down application server
   *
   */
  #[@afterClass]
  public static function shutdownApplicationServer() {
    self::$clientClassesLoader && \lang\ClassLoader::removeLoader(self::$clientClassesLoader);
  
    // Send shutdown message (this is not supported by live servers
    // but functionality added via EascMessageFactory::setHandler())
    try {
      $s= new Socket(self::$bindAddress[0], self::$bindAddress[1]);
      $s->connect();
      $s->write(pack('Nc4Na*', DEFAULT_PROTOCOL_MAGIC_NUMBER, 1, 0, 61, false, 0, null));
      $s->close();
    } catch (\lang\Throwable $e) {
      // Fall through, below should terminate the process anyway
    }

    $status= self::$serverProcess->out->readLine();
    if (!strlen($status) || '+' != $status{0}) {
      while ($l= self::$serverProcess->out->readLine()) {
        $status.= $l;
      }
      while ($l= self::$serverProcess->err->readLine()) {
        $status.= $l;
      }
      self::$serverProcess->close();
      throw new \lang\IllegalStateException($status);
    }

    self::$serverProcess->close();
  }
  
  /**
   * Sets up this unittest
   *
   */
  public function setUp() {
    try {
      $this->remote= Remote::forName('xp://'.self::$bindAddress[0].':'.self::$bindAddress[1]);
    } catch (\remote\RemoteException $e) {
      throw new \unittest\PrerequisitesNotMetError('Cannot setup client/server communication', $e);
    }
  }
  
  /**
   * Test lookup
   *
   */
  #[@test]
  public function lookupCalculator() {
    $calc= $this->remote->lookup('xp/test/Calculator');
    $this->assertSubclass($calc, 'beans.test.Calculator');
  }

  /**
   * Test lookup
   *
   */
  #[@test, @expect('remote.RemoteException')]
  public function lookupNonExistant() {
    $this->remote->lookup(':DOES_NOT_EXIST');
  }

  /**
   * Test calling a method
   *
   */
  #[@test]
  public function addMethod() {
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
