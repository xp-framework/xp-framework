<?php namespace net\xp_framework\unittest\logging;

use unittest\TestCase;
use util\log\StreamAppender;
use util\log\LogCategory;
use util\log\layout\PatternLayout;
use io\streams\MemoryOutputStream;


/**
 * TestCase
 *
 * @see      xp://util.log.StreamAppender
 */
class StreamAppenderTest extends TestCase {
  protected $out= null;
  protected $cat= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->out= new MemoryOutputStream();
    $this->cat= create(new LogCategory('default'))->withAppender(
      create(new StreamAppender($this->out))->withLayout(new PatternLayout('%l: %m%n'))
    );
  }
  
  /**
   * Test a debug() call
   *
   */
  #[@test]
  public function debug() {
    $this->cat->debug('Hello');
    $this->assertEquals("debug: Hello\n", $this->out->getBytes());
  }
 
  /**
   * Test a info() call
   *
   */
  #[@test]
  public function info() {
    $this->cat->info('Hello');
    $this->assertEquals("info: Hello\n", $this->out->getBytes());
  }

  /**
   * Test a warn() call
   *
   */
  #[@test]
  public function warn() {
    $this->cat->warn('Hello');
    $this->assertEquals("warn: Hello\n", $this->out->getBytes());
  }

  /**
   * Test a error() call
   *
   */
  #[@test]
  public function error() {
    $this->cat->error('Hello');
    $this->assertEquals("error: Hello\n", $this->out->getBytes());
  }
 }
