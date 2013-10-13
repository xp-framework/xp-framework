<?php namespace net\xp_framework\unittest\logging;

use util\log\BufferedAppender;
use util\log\layout\PatternLayout;


/**
 * TestCase for BufferedAppender
 *
 * @see   xp://util.log.BufferedAppender
 */
class BufferedAppenderTest extends AppenderTest {

  /**
   * Creates new appender fixture
   *
   * @return  util.log.BufferedAppender
   */
  protected function newFixture() {
    return create(new BufferedAppender())->withLayout(new PatternLayout("[%l] %m\n"));
  }

  /**
   * Test append() method
   */
  #[@test]
  public function buffer_initially_empty() {
    $this->assertEquals('', $this->newFixture()->getBuffer());
  }

  /**
   * Test append() method
   */
  #[@test]
  public function append_one_message() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $this->assertEquals(
      "[warn] Test\n",
      $fixture->getBuffer()
    );
  }

  /**
   * Test append() method
   */
  #[@test]
  public function append_two_messages() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $fixture->append($this->newEvent(\util\log\LogLevel::INFO, 'Just testing'));
    $this->assertEquals(
      "[warn] Test\n[info] Just testing\n",
      $fixture->getBuffer()
    );
  }

  /**
   * Test clear() method
   */
  #[@test]
  public function clear() {
    $fixture= $this->newFixture();
    $fixture->clear();
    $this->assertEquals('', $fixture->getBuffer());
  }

  /**
   * Test clear() method
   */
  #[@test]
  public function clear_after_appending() {
    $fixture= $this->newFixture();
    $fixture->append($this->newEvent(\util\log\LogLevel::WARN, 'Test'));
    $fixture->clear();
    $this->assertEquals('', $fixture->getBuffer());
  }
}
