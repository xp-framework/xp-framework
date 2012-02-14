<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.layout.PatternLayout',
    'util.log.LogContext'
  );

  /**
   * TestCase
   *
   * @see      xp://util.log.layout.PatternLayout
   */
  class PatternLayoutTest extends TestCase {

    /**
     * Test illegal format token %Q
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalFormatToken() {
      new PatternLayout('%Q');
    }
 
    /**
     * Test unterminated format token
     * 
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unterminatedFormatToken() {
      new PatternLayout('%');
    }
    
    /**
     * Creates a new logging event
     *
     * @return  util.log.LoggingEvent
     */
    protected function newLoggingEvent($context= array()) {
      return new LoggingEvent(
        new LogCategory('default'), 
        1258733284, 
        1214, 
        LogLevel::WARN, 
        array('Hello'),
        $context
      );   
    }

    /**
     * Test literal percent
     * 
     */
    #[@test]
    public function literalPercent() {
      $this->assertEquals(
        '100%',
        create(new PatternLayout('100%%'))->format($this->newLoggingEvent())
      );
    }

    /**
     * Test simple format:
     * <pre>
     *   INFO [default] Hello
     * </pre>
     */
    #[@test]
    public function simpleFormat() {
      $this->assertEquals(
        'WARN [default] Hello',
        create(new PatternLayout('%L [%c] %m'))->format($this->newLoggingEvent())
      );
    }

    /**
     * Test default format:
     * <pre>
     *   [16:08:04 1214 warn] Hello
     * </pre>
     */
    #[@test]
    public function defaultFormat() {
      $this->assertEquals(
        '[16:08:04 1214 warn] Hello',
        create(new PatternLayout('[%t %p %l] %m'))->format($this->newLoggingEvent())
      );
    }

    /**
     * Test %x evaluates to '' when no contet was given
     *
     */
    #[@test]
    public function noWhitespaceWhenNoContext() {
      $this->assertEquals(
        '[16:08:04 1214 warn] Hello',
        create(new PatternLayout('[%t %p %l] %x%m'))->format($this->newLoggingEvent())
      );
    }

    /**
     * If context available, add trailing space to separate
     * from message
     *
     */
    #[@test]
    public function whitespaceAddedForContext() {
      $this->assertEquals(
        '[16:08:04 1214 warn] Context Hello',
        create(new PatternLayout('[%t %p %l] %x%m'))->format($this->newLoggingEvent(array(new LogContext('Context'))))
      );
    }
  }
?>
