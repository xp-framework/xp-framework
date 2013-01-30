<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'lang.Module');

  /**
   * TestCase
   *
   * @see xp://lang.Module
   */
  class ModuleRegistrationTest extends TestCase {
    protected static $module;

    /**
     * Declares module used as fixture
     */
    #[@beforeClass]
    public static function declareModule() {
      self::$module= ClassLoader::declareModule(newinstance('lang.IClassLoader', array(), '{
        public function providesResource($name) { return TRUE; }
        public function providesClass($name) { return FALSE; }
        public function providesPackage($name) { return FALSE; }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { return "<?php module registration-fixture { } ?>"; }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function instanceId() { return "(registration-fixture)"; }
      }'));
    }

    /**
     * Test registerModule()
     *
     */
    #[@test]
    public function register() {
      ClassLoader::registerModule(self::$module);
      ClassLoader::removeModule(self::$module);
    }

    /**
     * Test registerModule()
     *
     */
    #[@test]
    public function cannot_be_registerd_twice() {
      ClassLoader::registerModule(self::$module);
      try {
        ClassLoader::registerModule(self::$module);
        ClassLoader::removeModule(self::$module);
        $this->fail('Expected exception not caught', NULL, 'lang.IllegalStateException');
      } catch (IllegalStateException $expected) {
        ClassLoader::removeModule(self::$module);  
        $this->assertEquals('Module "registration-fixture" already registered', $expected->getMessage());
      }
    }
  }
?>
