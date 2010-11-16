<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'lang.ChainedException'
  );

  /**
   * Test ChainedException class
   *
   * @see      xp://util.ChainedException
   * @purpose  Unit Test
   */
  class ChainedExceptionTest extends TestCase {

    /**
     * Tests a ChainedException without a cause
     *
     */
    #[@test]
    public function withoutCause() {
      $e= new ChainedException('Message', NULL);
      $this->assertEquals('Message', $e->getMessage()) &&
      $this->assertNull($e->getCause()) &&
      $this->assertFalse(strstr($e->toString(), 'Caused by'));
    }

    /**
     * Tests a ChainedException with a cause
     *
     */
    #[@test]
    public function withCause() {
      $e= new ChainedException('Message', new IllegalArgumentException('Arg'));
      $this->assertEquals('Message', $e->getMessage()) &&
      $this->assertInstanceOf('lang.IllegalArgumentException', $e->getCause()) &&
      $this->assertEquals('Arg', $e->cause->getMessage()) &&
      $this->assertFalse(!strstr($e->toString(), 'Caused by Exception lang.IllegalArgumentException (Arg)'));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function commonElements() {
      $e= new ChainedException('Message', new IllegalArgumentException('Arg'));
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }

    /**
     * Tests number of common elements is reported in  toString() output
     *
     */
    #[@test]
    public function chainedCommonElements() {
      $e= new ChainedException('Message', new ChainedException('Message2', new IllegalArgumentException('Arg')));
      $this->assertEquals(2, preg_match_all('/  ... [0-9]+ more/', $e->toString(), $matches));
    }

    /**
     * Tests completely common stack trace
     *
     */
    #[@test]
    public function completelyCommonStackTrace() {
      $trace = array(
        new StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
        new StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
      );
      $e= new XPException('Test');
      $e->trace= $trace;
      $c= new ChainedException($e->getMessage(), $e);
      $c->trace= $trace;
      
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
    }

    /**
     * Tests completely common stack trace
     *
     */
    #[@test]
    public function causeWithUncommonElements() {
      $trace = array(
        new StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
        new StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
      );
      $e= new XPException('Test');
      $e->trace= array_merge(
        array(new StackTraceElement(NULL, 'ReflectionMethod', 'invokeArgs', 0, array(), NULL)),
        array(new StackTraceElement('Method.class.php', 'Method', 'invoke', 0, array(), NULL)),
        $trace
      );
      $c= new ChainedException($e->getMessage(), $e);
      $c->trace= $trace;
      
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
    }

    /**
     * Tests completely common stack trace
     *
     */
    #[@test]
    public function chainedWithUncommonElements() {
      $trace = array(
        new StackTraceElement('Test.class.php', 'Test', 'test', 0, array(), NULL),
        new StackTraceElement('TestSuite.class.php', 'TestSuite', '__construct', 0, array('Test::test'), NULL),
      );
      $e= new XPException('Test');
      $e->trace= $trace;
      $c= new ChainedException($e->getMessage(), $e);
      $c->trace= array_merge(
        array(new StackTraceElement(NULL, 'ReflectionMethod', 'invokeArgs', 0, array(), NULL)),
        array(new StackTraceElement('Method.class.php', 'Method', 'invoke', 0, array(), NULL)),
        $trace
      );
      
      $this->assertEquals(1, preg_match_all('/  ... [0-9]+ more/', $c->toString(), $matches), $c->toString());
    }
  }
?>
