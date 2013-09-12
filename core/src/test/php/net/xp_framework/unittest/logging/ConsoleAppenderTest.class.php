<?php namespace net\xp_framework\unittest\logging;

use unittest\TestCase;
use util\cmd\Console;
use util\log\ConsoleAppender;
use util\log\LogCategory;
use util\log\Layout;
use io\streams\MemoryOutputStream;


/**
 * TestCase
 *
 * @see   xp://util.cmd.Console
 * @see   xp://util.log.ConsoleAppender
 */
class ConsoleAppenderTest extends TestCase {
  protected $cat= null;
  protected $stream= null;

  /**
   * Sets up test case and backups Console::$err stream.
   *
   */
  public function setUp() {
    $this->cat= create(new LogCategory('default'))->withAppender(
      create(new ConsoleAppender())->withLayout(newinstance('util.log.Layout', array(), '{
        public function format(LoggingEvent $event) {
          return implode(" ", $event->getArguments());
        }
      }'))
    );
    $this->stream= Console::$err->getStream();
  }

  /**
   * Restores Console::$err stream.
   *
   */
  public function tearDown() {
    Console::$err->setStream($this->stream);
  }

  /**
   * Test
   *
   */
  #[@test]
  public function appendMessage() {
    with ($message= 'Test', $stream= new MemoryOutputStream()); {
      Console::$err->setStream($stream);
      $this->cat->warn($message);
      $this->assertEquals($message, $stream->getBytes());
    }
  }
}
