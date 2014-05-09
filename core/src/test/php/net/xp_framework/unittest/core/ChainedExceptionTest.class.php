<?php namespace net\xp_framework\unittest\core;
 
use lang\ChainedException;

/**
 * Test ChainedException class
 *
 * @see  xp://lang.ChainedException
 */
class ChainedExceptionTest extends \unittest\TestCase {

  #[@test]
  public function withoutCause() {
    $e= new ChainedException('Message', NULL);
    $this->assertEquals('Message', $e->getMessage()) &&
    $this->assertNull($e->getCause()) &&
    $this->assertFalse(strstr($e->toString(), 'Caused by'));
  }

  #[@test]
  public function withCause() {
    $e= new ChainedException('Message', new \lang\IllegalArgumentException('Arg'));
    $this->assertEquals('Message', $e->getMessage()) &&
    $this->assertInstanceOf('lang.IllegalArgumentException', $e->getCause()) &&
    $this->assertEquals('Arg', $e->cause->getMessage()) &&
    $this->assertFalse(!strstr($e->toString(), 'Caused by Exception lang.IllegalArgumentException (Arg)'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function withCause_must_be_a_throwable() {
    new ChainedException('Message', 'Anything...');
  }

  #[@test]
  public function commonElements() {
    $e= new ChainedException('Message', new \lang\IllegalArgumentException('Arg'));
    $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
  }

  #[@test]
  public function chainedCommonElements() {
    $e= new ChainedException('Message', new ChainedException('Message2', new \lang\IllegalArgumentException('Arg')));
    $this->assertEquals(2, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
  }

  #[@test]
  public function completelyCommonStackTrace() {
    $trace = array(
      new \lang\StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
      new \lang\StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
    );
    $e= new \lang\XPException('Test');
    $e->trace= $trace;
    $c= new ChainedException($e->getMessage(), $e);
    $c->trace= $trace;
    
    $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
  }

  #[@test]
  public function causeWithUncommonElements() {
    $trace = array(
      new \lang\StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
      new \lang\StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
    );
    $e= new \lang\XPException('Test');
    $e->trace= array_merge(
      array(new \lang\StackTraceElement(NULL, 'ReflectionMethod', 'invokeArgs', 0, array(), NULL)),
      array(new \lang\StackTraceElement('Method.class.php', 'Method', 'invoke', 0, array(), NULL)),
      $trace
    );
    $c= new ChainedException($e->getMessage(), $e);
    $c->trace= $trace;
    
    $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
  }

  #[@test]
  public function chainedWithUncommonElements() {
    $trace = array(
      new \lang\StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
      new \lang\StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
    );
    $e= new \lang\XPException('Test');
    $e->trace= $trace;
    $c= new ChainedException($e->getMessage(), $e);
    $c->trace= array_merge(
      array(new \lang\StackTraceElement(NULL, 'ReflectionMethod', 'invokeArgs', 0, array(), NULL)),
      array(new \lang\StackTraceElement('Method.class.php', 'Method', 'invoke', 0, array(), NULL)),
      $trace
    );
    
    $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
  }
}
