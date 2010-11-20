<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.cmd.Console',
    'util.log.ConsoleAppender',
    'util.log.LogCategory',
    'util.log.Layout',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see   xp://util.cmd.Console
   * @see   xp://util.log.ConsoleAppender
   */
  class ConsoleAppenderTest extends TestCase {
    protected $cat= NULL;
    protected $stream= NULL;
  
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
?>
