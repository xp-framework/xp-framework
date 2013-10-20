<?php namespace net\xp_framework\unittest\logging;

use unittest\TestCase;
use util\log\Appender;
use util\log\LogCategory;
use util\log\layout\PatternLayout;
use util\collections\Vector;


/**
 * TestCase
 *
 * @see      xp://util.log.Appender
 */
class LogAppenderTest extends TestCase {
  protected $fixture= null;
  protected $events= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->events= create('new Vector<String>()');
    $appender= newinstance('util.log.Appender', array($this->events), '{
      private $events= NULL;

      public function __construct($events) {
        $this->events= $events;
      }

      public function append(LoggingEvent $event) {
        $this->events[]= new String($this->layout->format($event));
      }
    }');
    $this->fixture= create(new LogCategory('default'))
      ->withAppender($appender->withLayout(new PatternLayout('[%l] %m')))
    ;
  }
  
  /**
   * Test
   *
   */
  #[@test]
  public function info() {
    $this->fixture->info('Hello');
    $this->assertEquals(new \lang\types\String('[info] Hello'), $this->events[0]);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function infoWithMultipleArguments() {
    $this->fixture->info('Hello', 'World');
    $this->assertEquals(new \lang\types\String('[info] Hello World'), $this->events[0]);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function warn() {
    $this->fixture->warn('Hello');
    $this->assertEquals(new \lang\types\String('[warn] Hello'), $this->events[0]);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function debug() {
    $this->fixture->debug('Hello');
    $this->assertEquals(new \lang\types\String('[debug] Hello'), $this->events[0]);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function error() {
    $this->fixture->error('Hello');
    $this->assertEquals(new \lang\types\String('[error] Hello'), $this->events[0]);
  }
}
