<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.HttpScriptlet',
    'lang.System',
    'io.Folder',
    'peer.URL'
  );


  /**
   * Base class for scriptlet test cases. Ensures sessions are stored
   * in a temporary directory which is removed after tests in the sub-
   * classes are run.
   *
   */
  abstract class ScriptletTestCase extends TestCase {
    protected static $temp= NULL;
  
    static function __static() {
      if (!function_exists('getallheaders')) {
        function getallheaders() { return array(); }
      }
    }

    /**
     * Set session path to temporary directory
     *
     */
    #[@beforeClass]
    public static function prepareTempDir() {
      self::$temp= new Folder(System::tempDir(), md5(uniqid()));
      self::$temp->create();
      session_save_path(self::$temp->getURI());
    }

    /**
     * Destroy session
     *
     */
    public function tearDown() {
      session_id(NULL);
    }

    /**
     * Cleanup temporary directory
     *
     */
    #[@afterClass]
    public static function cleanupTempDir() {
      self::$temp->unlink();
    }
  }
?>
