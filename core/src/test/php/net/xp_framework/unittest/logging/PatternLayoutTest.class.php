<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.layout.PatternLayout',
    'util.log.context.MappedLogContext'
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
    protected function newLoggingEvent() {
      return new LoggingEvent(
        new LogCategory('default'), 
        1258733284, 
        1214, 
        LogLevel::WARN, 
        array('Hello')
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
     * Test format token %x
     *
     */
    #[@test]
    public function tokenContext() {
      $context= new MappedLogContext();
      $context->put('key1', 'val1');

      $event= new LoggingEvent(
        new LogCategory('default', LogLevel::ALL, $context),
        1258733284,
        1,
        LogLevel::INFO,
        array('Hello')
      );

      $this->assertEquals(
        'key1=val1',
        create(new PatternLayout('%x'))->format($event)
      );
    }
  }
?>
