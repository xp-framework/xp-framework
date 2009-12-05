<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.log.Logger',
    'util.log.Appender',
    'util.log.LogAppender',
    'util.log.layout.PatternLayout'
  );

  /**
   * Tests LogCategory class
   *
   * @purpose  Unit Test
   */
  class LogCategoryTest extends TestCase {
    public $cat= NULL;
    
    /**
     * Setup method. Creates logger and cat member for easier access to
     * the Logger instance
     *
     */
    public function setUp() {
      $this->cat= new LogCategory('test');
    }
    
    /**
     * Create a mock appender which simply stores all messages passed to 
     * its append() method.
     *
     * @return  util.log.Appender
     */
    protected function mockAppender() {
      $appender= newinstance('util.log.Appender', array(), '{
        public $messages= array();
        
        public function append(LoggingEvent $event) {
          $this->messages[]= array(
            strtolower(LogLevel::nameOf($event->getLevel())), 
            $this->layout->format($event)
          );
        }
      }');
      return $appender->withLayout(new PatternLayout('%m'));
    }
    
    /**
     * Helper method
     *
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  unittest.AssertionFailedError
     */
    protected function assertLog($method, $args= array('Argument')) {
      $app= $this->cat->addAppender($this->mockAppender());
      call_user_func_array(array($this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)$method, $args)), $app->messages);
    }

    /**
     * Helper method
     *
     * @param   string method
     * @param   mixed[] args default ["Argument"]
     * @throws  unittest.AssertionFailedError
     */
    protected function assertLogf($method, $args= array('Argument')) {
      $app= $this->cat->addAppender($this->mockAppender());
      call_user_func_array(array($this->cat, $method), $args);
      $this->assertEquals(array(array_merge((array)substr($method, 0, -1), (array)vsprintf(array_shift($args), $args))), $app->messages);
    }
    
    /**
     * Ensure the logger category initially has no appenders
     *
     */
    #[@test]
    public function initiallyNoAppenders() {
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests adding an appender returns the added appender
     *
     */
    #[@test]
    public function addAppenderReturnsAddedAppender() {
      $appender= $this->mockAppender();
      $this->assertEquals($appender, $this->cat->addAppender($appender));
    }

    /**
     * Tests adding an appender returns the log category
     *
     */
    #[@test]
    public function withAppenderReturnsCategory() {
      $this->assertEquals($this->cat, $this->cat->withAppender($this->mockAppender()));
    }

    /**
     * Tests hasAppenders() and addAppender() methods
     *
     */
    #[@test]
    public function hasAppendersAfterAdding() {
      $this->cat->addAppender($this->mockAppender());
      $this->assertTrue($this->cat->hasAppenders());
    }

    /**
     * Tests hasAppenders() and removeAppender() methods
     *
     */
    #[@test]
    public function hasNoMoreAppendersAfterRemoving() {
      $a= $this->cat->addAppender($this->mockAppender());
      $this->cat->removeAppender($a);
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests addAppender() method
     *
     */
    #[@test]
    public function addAppenderTwice() {
      $a= $this->mockAppender();
      $this->cat->addAppender($a);
      $this->cat->addAppender($a);
      $this->cat->removeAppender($a);
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests addAppender() and removeAppender() methods
     *
     */
    #[@test]
    public function addAppenderTwiceWithDifferentFlags() {
      $a= $this->mockAppender();
      $this->cat->addAppender($a, LogLevel::INFO);
      $this->cat->addAppender($a, LogLevel::WARN);
      $this->cat->removeAppender($a, LogLevel::INFO);
      $this->assertTrue($this->cat->hasAppenders());
      $this->cat->removeAppender($a, LogLevel::WARN);
      $this->assertFalse($this->cat->hasAppenders());
    }

    /**
     * Tests adding an appender sets default layout if appender does not
     * have a layout.
     *
     */
    #[@test]
    public function addAppenderSetsDefaultLayout() {
      $appender= newinstance('util.log.Appender', array(), '{
        public function append(LoggingEvent $event) { }
      }');
      $this->cat->addAppender($appender);
      $this->assertClass($appender->getLayout(), 'util.log.layout.DefaultLayout');
    }

    /**
     * Tests adding an appender does not overwrite layout
     *
     */
    #[@test]
    public function addAppenderDoesNotOverwriteLayout() {
      $appender= newinstance('util.log.Appender', array(), '{
        public function append(LoggingEvent $event) { }
      }');
      $this->cat->addAppender($appender->withLayout(new PatternLayout('%m')));
      $this->assertClass($appender->getLayout(), 'util.log.layout.PatternLayout');
    }

    /**
     * Tests adding an appender sets default layout if appender does not
     * have a layout.
     *
     */
    #[@test]
    public function withAppenderSetsLayout() {
      $appender= newinstance('util.log.Appender', array(), '{
        public function append(LoggingEvent $event) { }
      }');
      $this->cat->withAppender($appender);
      $this->assertClass($appender->getLayout(), 'util.log.layout.DefaultLayout');
    }

    /**
     * Tests adding an appender does not overwrite layout
     *
     */
    #[@test]
    public function withAppenderDoesNotOverwriteLayout() {
      $appender= newinstance('util.log.Appender', array(), '{
        public function append(LoggingEvent $event) { }
      }');
      $this->cat->withAppender($appender->withLayout(new PatternLayout('%m')));
      $this->assertClass($appender->getLayout(), 'util.log.layout.PatternLayout');
    }

    /**
     * Tests equals() method
     *
     */
    #[@test]
    public function logCategoriesWithSameIdentifierAreEqual() {
      $this->assertEquals(new LogCategory('test'), $this->cat);
    }

    /**
     * Tests equals() method
     *
     */
    #[@test]
    public function logCategoriesDifferingAppendersNotEqual() {
      $this->assertNotEquals(
        new LogCategory('test'), 
        $this->cat->withAppender($this->mockAppender())
      );
    }

    /**
     * Tests equals() method
     *
     */
    #[@test]
    public function logCategoriesAppendersDifferingInFlagsNotEqual() {
      $appender= $this->mockAppender();
      $this->assertNotEquals(
        create(new LogCategory('test'))->withAppender($appender, LogLevel::WARN), 
        $this->cat->withAppender($appender)
      );
    }

    /**
     * Tests equals() method
     *
     */
    #[@test]
    public function logCategoriesSameAppendersEqual() {
      $appender= $this->mockAppender();
      $this->assertEquals(
        create(new LogCategory('test'))->withAppender($appender), 
        $this->cat->withAppender($appender)
      );
    }

    /**
     * Tests debug() method
     *
     */
    #[@test]
    public function debug() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests debugf() method
     *
     */
    #[@test]
    public function debugf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests info() method
     *
     */
    #[@test]
    public function info() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests infof() method
     *
     */
    #[@test]
    public function infof() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests warn() method
     *
     */
    #[@test]
    public function warn() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests warnf() method
     *
     */
    #[@test]
    public function warnf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests error() method
     *
     */
    #[@test]
    public function error() {
      $this->assertLog(__FUNCTION__);
    }

    /**
     * Tests errorf() method
     *
     */
    #[@test]
    public function errorf() {
      $this->assertLogf(__FUNCTION__, array('Hello %s', __CLASS__));
    }

    /**
     * Tests mark() method
     *
     */
    #[@test]
    public function mark() {
      $app= $this->cat->addAppender($this->mockAppender());
      $this->cat->mark();
      $this->assertEquals(array(array('info', str_repeat('-', 72))), $app->messages); 
    }

    /**
     * Tests flags
     *
     */
    #[@test]
    public function warningMessageOnlyGetsAppendedToWarnAppender() {
      $app1= $this->cat->addAppender($this->mockAppender(), LogLevel::INFO);
      $app2= $this->cat->addAppender($this->mockAppender(), LogLevel::WARN);
      $this->cat->warn('Test');
      $this->assertEquals(array(), $app1->messages);
      $this->assertEquals(array(array('warn', 'Test')), $app2->messages); 
    }

    /**
     * Tests adding a deprecated util.log.LogAppender results in it being
     * wrapped in a util.log.LogAppenderAdapter
     *
     * @deprecated
     */
    #[@test]
    public function logAppenderAdapter() {
      $added= $this->cat->addAppender(newinstance('util.log.LogAppender', array(), '{
        public function append() { }
      }'));
      $this->assertClass($added, 'util.log.LogAppenderAdapter');
    }
  }
?>
