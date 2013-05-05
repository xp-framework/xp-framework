<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.logging.AppenderTest',
    'util.log.SmtpAppender',
    'util.log.layout.PatternLayout'
  );

  /**
   * TestCase for SmtpAppender
   *
   * @see   xp://util.log.SmtpAppender
   */
  class SmtpAppenderTest extends AppenderTest {

    /**
     * Creates new SMTP appender fixture
     *
     * @param   string prefix
     * @param   bool sync
     * @return  util.log.SmtpAppender
     */
    protected function newFixture($prefix, $sync) {
      $appender= newinstance('util.log.SmtpAppender', array('test@example.com', $prefix, $sync), '{
        public $sent= array();
        protected function send($prefix, $content) {
          $this->sent[]= array($prefix, $content);
        }
      }');
      return $appender->withLayout(new PatternLayout('[%l] %m'));
    }

    /**
     * Test append() method
     */
    #[@test]
    public function append_sync() {
      $fixture= $this->newFixture('test', $sync= TRUE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $this->assertEquals(array(array('test', '[warn] Test')), $fixture->sent);
    }

    /**
     * Test append() method
     */
    #[@test]
    public function append_sync_two_messages() {
      $fixture= $this->newFixture('test', $sync= TRUE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
      $this->assertEquals(
        array(array('test', '[warn] Test'), array('test', '[info] Just testing')),
        $fixture->sent
      );
    }

    /**
     * Test finalize() method
     */
    #[@test]
    public function finalize_sync() {
      $fixture= $this->newFixture('test', $sync= TRUE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $sent= $fixture->sent;
      $fixture->finalize();
      $this->assertEquals($sent, $fixture->sent);
    }

    /**
     * Test append() method
     */
    #[@test]
    public function append_async() {
      $fixture= $this->newFixture('test', $sync= FALSE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $this->assertEquals(array(), $fixture->sent);
    }

    /**
     * Test finalize() method
     */
    #[@test]
    public function finalize_async_no_messages() {
      $fixture= $this->newFixture('test', $sync= FALSE);
      $fixture->finalize();
      $this->assertEquals(array(), $fixture->sent);
    }

    /**
     * Test finalize() method
     */
    #[@test]
    public function finalize_async() {
      $fixture= $this->newFixture('test', $sync= FALSE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $fixture->finalize();
      $this->assertEquals(
        array(array('test [1 entries]', "[warn] Test\n")),
        $fixture->sent
      );
    }

    /**
     * Test finalize() method
     */
    #[@test]
    public function finalize_async_two_messages() {
      $fixture= $this->newFixture('test', $sync= FALSE);
      $fixture->append($this->newEvent(LogLevel::WARN, 'Test'));
      $fixture->append($this->newEvent(LogLevel::INFO, 'Just testing'));
      $fixture->finalize();
      $this->assertEquals(
        array(array('test [2 entries]', "[warn] Test\n[info] Just testing\n")),
        $fixture->sent
      );
    }
  }
?>
