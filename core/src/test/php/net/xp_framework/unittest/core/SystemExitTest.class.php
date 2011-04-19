<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.System',
    'lang.SystemExit'
  );

  /**
   * TestCase for System exit
   *
   * @see      xp://lang.SystemExit
   */
  class SystemExitTest extends TestCase {
    protected static $exiterClass= NULL;
  
    /**
     * Defines Exiter class
     *
     */
    #[@beforeClass]
    public static function defineExiterClass() {
      self::$exiterClass= ClassLoader::defineClass('net.xp_framework.unittest.core.Exiter', 'lang.Object', array(), '{
        public function __construct() { throw new SystemExit(0); }
        public static function doExit() { new self(); }
      }');
    }
  
    /**
     * Test stack trace is empty
     *
     */
    #[@test]
    public function noStack() {
      $this->assertEquals(array(), create(new SystemExit(0))->getStackTrace());
    }

    /**
     * Test getCode()
     *
     */
    #[@test]
    public function zeroExitCode() {
      $this->assertEquals(0, create(new SystemExit(0))->getCode());
    }

    /**
     * Test getCode()
     *
     */
    #[@test]
    public function nonZeroExitCode() {
      $this->assertEquals(1, create(new SystemExit(1))->getCode());
    }

    /**
     * Test getMessage()
     *
     */
    #[@test]
    public function noMessage() {
      $this->assertEquals('', create(new SystemExit(0))->getMessage());
    }
    
    /**
     * Test getMessage()
     *
     */
    #[@test]
    public function message() {
      $this->assertEquals('Hello', create(new SystemExit(1, 'Hello'))->getMessage());
    }
    
    /**
     * Test invoking a method by reflection
     *
     */
    #[@test]
    public function invoke() {
      try {
        self::$exiterClass->getMethod('doExit')->invoke(NULL);
        $this->fail('Expected', NULL, 'lang.SystemExit');
      } catch (SystemExit $e) {
        // OK
      }
    }

    /**
     * Test constructing an instance by reflection
     *
     */
    #[@test]
    public function construct() {
      try {
        self::$exiterClass->newInstance();
        $this->fail('Expected', NULL, 'lang.SystemExit');
      } catch (SystemExit $e) {
        // OK
      }
    }

    /**
     * Test Runtime::halt()
     *
     */
    #[@test]
    public function systemExit() {
      try {
        Runtime::halt();
        $this->fail('Expected', NULL, 'lang.SystemExit');
      } catch (SystemExit $e) {
        $this->assertEquals(0, $e->getCode());
      }
    }

    /**
     * Test Runtime::halt()
     *
     */
    #[@test]
    public function systemExitWithNonZeroExitCode() {
      try {
        Runtime::halt(127);
        $this->fail('Expected', NULL, 'lang.SystemExit');
      } catch (SystemExit $e) {
        $this->assertEquals(127, $e->getCode());
      }
    }
  }
?>
