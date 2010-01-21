<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'io.streams.Streams', 'io.streams.MemoryOutputStream');

  /**
   * Test the XP exception mechanism
   *
   * @purpose  Testcase
   */
  class ExceptionsTest extends TestCase {

    /**
     * Basics: Tests nothing is caught when nothing is thrown
     *
     */
    #[@test]
    public function noException() {
      try {
        // Nothing
      } catch (XPException $caught) {
        return $this->fail('Caught an exception but none where thrown', $caught);
      }
    }

    /**
     * Basics: Tests thrown exception is caught
     *
     */
    #[@test]
    public function thrownExceptionCaught() {
      try {
        throw new XPException('Test');
      } catch (XPException $caught) {
        $this->assertInstanceOf('Exception', $caught);
        delete($caught);
        return TRUE;
      }

      $this->fail('Thrown Exception not caught');
    }

    /**
     * Basics: Tests thrown exception is caught in the correct catch
     * block.
     *
     */
    #[@test]
    public function multipleCatches() {
      try {
        throw new XPException('Test');
      } catch (IllegalArgumentException $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'IllegalArgumentException');
      } catch (XPException $caught) {
        return TRUE;
      } catch (Throwable $caught) {
        return $this->fail('Exception should have been caught in Exception block', 'Throwable');
      }

      $this->fail('Thrown Exception not caught');
    }

    /**
     * Tests getStackTrace() method
     *
     */
    #[@test]
    public function stackTrace() {
      $trace= create(new Throwable('Test'))->getStackTrace();
      $this->assertArray($trace);
      $this->assertNotEmpty($trace);
      foreach ($trace as $element) {
        $this->assertInstanceOf('lang.StackTraceElement', $element);
      }
    }

    /**
     * Tests getStackTrace() method
     *
     */
    #[@test]
    public function firstFrame() {
      $trace= create(new Throwable('Test'))->getStackTrace();
      
      $this->assertEquals(get_class($this), $trace[0]->class);
      $this->assertEquals($this->getName(), $trace[0]->method);
    }

    /**
     * Tests equals() method
     *
     */
    #[@test]
    public function allExceptionsAreUnique() {
      $this->assertNotEquals(new Throwable('Test'), new Throwable('Test'));
    }

    /**
     * Tests hashCode() method
     *
     */
    #[@test]
    public function hashCodesAreUnique() {
      $this->assertNotEquals(
        create(new Throwable('Test'))->hashCode(),
        create(new Throwable('Test'))->hashCode()
      );
    }

    /**
     * Tests getMessage() method
     *
     */
    #[@test]
    public function message() {
      $this->assertEquals('Test', create(new Throwable('Test'))->getMessage());
    }

    /**
     * Tests getClass() method
     *
     */
    #[@test]
    public function classMethod() {
      $this->assertEquals(XPClass::forName('lang.Throwable'), create(new Throwable('Test'))->getClass());
    }

    /**
     * Tests getClassName() method
     *
     */
    #[@test]
    public function classNameMethod() {
      $this->assertEquals('lang.Throwable', create(new Throwable('Test'))->getClassName());
    }

    /**
     * Tests compoundMessage() method
     *
     */
    #[@test]
    public function compoundMessage() {
      $this->assertEquals(
        'Exception lang.Throwable (Test)', 
        create(new Throwable('Test'))->compoundMessage()
      );
    }

    /**
     * Tests printStackTrace() method
     *
     */
    #[@test]
    public function printStackTrace() {
      $out= new MemoryOutputStream();
      $e= new Throwable('Test');
      create($e)->printStackTrace(Streams::writeableFd($out));
      $this->assertEquals($e->toString(), $out->getBytes());
    }
    
    /**
     * Test raise() works when passing the exception's message 
     * directly and as single argument
     */
    #[@test]
    public function raiseWithOneArgument() {
      try {
        raise('lang.IllegalArgumentException', 'This is the message');
        $this->fail('Exception has not been thrown', NULL, NULL);
      } catch (IllegalArgumentException $e) {
        $this->assertEquals('This is the message', $e->getMessage());
      }
    }
    
    #[@test]
    public function raiseWithMoreArguments() {
      try {
        raise('lang.MethodNotImplementedException', 'This is the message', __FUNCTION__);
        $this->fail('Exception has not been thrown', NULL, NULL);
      } catch (MethodNotImplementedException $e) {
        $this->assertEquals('This is the message', $e->getMessage());
        $this->assertEquals(__FUNCTION__, $e->method);
      }
    }
  }
?>
