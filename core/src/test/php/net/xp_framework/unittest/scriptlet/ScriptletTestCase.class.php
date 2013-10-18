<?php namespace net\xp_framework\unittest\scriptlet;

use unittest\TestCase;
use scriptlet\HttpScriptlet;
use lang\System;
use io\Folder;
use peer\URL;

/**
 * Base class for scriptlet test cases. Ensures sessions are stored
 * in a temporary directory which is removed after tests in the sub-
 * classes are run.
 *
 */
abstract class ScriptletTestCase extends TestCase {
  protected static $temp= null;

  static function __static() {
    if (!function_exists('getallheaders')) {
      eval('function getallheaders() { return array(); }');
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
   * Cleanup temporary directory
   *
   */
  #[@afterClass]
  public static function cleanupTempDir() {
    self::$temp->unlink();
  }
}
