<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use io\streams\Streams;
use io\streams\MemoryOutputStream;
use lang\Throwable;

/**
 * Test the XP exception mechanism
 *
 * @purpose  Testcase
 */
class ExceptionsTest extends TestCase {

  #[@test]
  public function noException() {
    try {
      // Nothing
    } catch (\lang\XPException $caught) {
      $this->fail('Caught an exception but none where thrown', $caught);
    }
  }

  #[@test]
  public function thrownExceptionCaught() {
    try {
      throw new Throwable('Test');
    } catch (Throwable $caught) {
      $this->assertInstanceOf('lang.Throwable', $caught);
      delete($caught);
      return true;
    }

    $this->fail('Thrown Exception not caught');
  }

  #[@test]
  public function multipleCatches() {
    try {
      throw new \lang\XPException('Test');
    } catch (\lang\IllegalArgumentException $caught) {
      return $this->fail('Exception should have been caught in Exception block', 'IllegalArgumentException');
    } catch (\lang\XPException $caught) {
      return true;
    } catch (Throwable $caught) {
      return $this->fail('Exception should have been caught in Exception block', 'Throwable');
    }

    $this->fail('Thrown Exception not caught');
  }

  #[@test]
  public function stackTrace() {
    $trace= create(new Throwable('Test'))->getStackTrace();
    $this->assertArray($trace);
    $this->assertNotEmpty($trace);
    foreach ($trace as $element) {
      $this->assertInstanceOf('lang.StackTraceElement', $element);
    }
  }

  #[@test]
  public function firstFrame() {
    $trace= create(new Throwable('Test'))->getStackTrace();
    
    $this->assertEquals(get_class($this), $trace[0]->class);
    $this->assertEquals($this->getName(), $trace[0]->method);
    $this->assertEquals(NULL, $trace[0]->file);
    $this->assertEquals(0, $trace[0]->line);
    $this->assertEquals(array(), $trace[0]->args);
    $this->assertEquals('', $trace[0]->message);
  }

  #[@test]
  public function allExceptionsAreUnique() {
    $this->assertNotEquals(new Throwable('Test'), new Throwable('Test'));
  }

  #[@test]
  public function hashCodesAreUnique() {
    $this->assertNotEquals(
      create(new Throwable('Test'))->hashCode(),
      create(new Throwable('Test'))->hashCode()
    );
  }

  #[@test]
  public function message() {
    $this->assertEquals('Test', create(new Throwable('Test'))->getMessage());
  }

  #[@test]
  public function classMethod() {
    $this->assertEquals(\lang\XPClass::forName('lang.Throwable'), create(new Throwable('Test'))->getClass());
  }

  #[@test]
  public function classNameMethod() {
    $this->assertEquals('lang.Throwable', create(new Throwable('Test'))->getClassName());
  }

  #[@test]
  public function compoundMessage() {
    $this->assertEquals(
      'Exception lang.Throwable (Test)', 
      create(new Throwable('Test'))->compoundMessage()
    );
  }

  #[@test]
  public function printStackTrace() {
    $out= new MemoryOutputStream();
    $e= new Throwable('Test');
    create($e)->printStackTrace(Streams::writeableFd($out));
    $this->assertEquals($e->toString(), $out->getBytes());
  }
  
  #[@test]
  public function raiseWithOneArgument() {
    try {
      raise('lang.IllegalArgumentException', 'This is the message');
      $this->fail('Exception has not been thrown', NULL, NULL);
    } catch (\lang\IllegalArgumentException $e) {
      $this->assertEquals('This is the message', $e->getMessage());
    }
  }
  
  #[@test]
  public function raiseWithMoreArguments() {
    try {
      raise('lang.MethodNotImplementedException', 'This is the message', __FUNCTION__);
      $this->fail('Exception has not been thrown', NULL, NULL);
    } catch (\lang\MethodNotImplementedException $e) {
      $this->assertEquals('This is the message', $e->getMessage());
      $this->assertEquals(__FUNCTION__, $e->method);
    }
  }
}
