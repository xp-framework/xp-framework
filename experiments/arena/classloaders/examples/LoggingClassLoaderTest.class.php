<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.log.Logger',
    'examples.LoggingClassLoader',
    'util.log.BufferedAppender'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class LoggingClassLoaderTest extends TestCase {
    protected static
      $appender= NULL;

    static function __static() {
      self::$appender= new BufferedAppender();
      ClassLoader::registerLoader(
        new LoggingClassLoader(Logger::getInstance()->getCategory()->withAppender(self::$appender)),
        TRUE
      );
    }

    /**
     * Setup this testcase
     *
     */
    public function setUp() {
      self::$appender->clear();
    }
    
    /**
     * Assertion helper
     *
     * @param   string expected
     * @throws  unittest.AssertionFailedError
     */
    protected function assertBufferContains($expected) {
      with ($buf= self::$appender->getBuffer()); {
        if (FALSE !== strpos($buf, $expected)) return;
        $this->fail('notcontained', $buf, $expected);
      }
    }
    
    /**
     * Test XPClass::forName() will trigger the LoggingClassLoader
     * if a non-existant class is requested
     *
     */
    #[@test]
    public function classForName() {
      $this->assertFalse(class_exists('Binford'));
      XPClass::forName('util.Binford');
      $this->assertBufferContains('Provides class: util.Binford');
    }

    /**
     * Test XPClass::forName() will NOT trigger the LoggingClassLoader
     * if an existant class is requested
     *
     */
    #[@test]
    public function classForExistingName() {
      $this->assertTrue(class_exists('Binford'));
      XPClass::forName('util.Binford');
      $this->assertEquals('', self::$appender->getBuffer());
    }
  }
?>
