<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestAction');

  /**
   * Only runs this testcase on a given platform
   * 
   * The platform annotation may be written as "WIN" (meaning this works only 
   * on operating systems whose names contain "WIN" - e.g. Windows)  or as 
   * "!BSD" (this means this test will not run on OSes with "BSD" in their 
   * names but on any other)
   */
  class IsPlatform extends Object implements TestAction {
    protected $platform= '';
    protected static $os= '';

    static function __static() {
      if (getenv('ANDROID_ROOT')) {
        self::$os= 'ANDROID';         // Otherwise not distinguishable
      } else {
        self::$os= PHP_OS;
      }
    }

    /**
     * Create a new IsPlatform match
     *
     * @param string platform A pattern to match against PHP_OS, case-insensitively
     */
    public function __construct($platform) {
      $this->platform= $platform;
    }

    /**
     * This method gets invoked before a test method is invoked, and before
     * the setUp() method is called.
     *
     * @param  unittest.TestCase $t
     * @throws unittest.PrerequisitesNotMetError
     */
    public function beforeTest(TestCase $t) { 
      if ('!' === $this->platform{0}) {
        $r= !preg_match('/'.substr($this->platform, 1).'/i', self::$os);
      } else {
        $r= preg_match('/'.$this->platform.'/i', self::$os);
      }
      
      if (!$r) {
        throw new PrerequisitesNotMetError('Test not intended for this platform ('.self::$os.')', NULL, array($this->platform));
      }
    }

    /**
     * This method gets invoked after the test method is invoked and regard-
     * less of its outcome, after the tearDown() call has run.
     *
     * @param  unittest.TestCase $t
     */
    public function afterTest(TestCase $t) {
      // Empty
    }
  }
?>