<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see  xp://lang.ClassLoader#declareModule
   */
  class ModuleDeclarationTest extends TestCase {

    /**
     * Assertion helper
     *
     * @param  string name The expected name
     * @param  string version The expected version
     * @param  string moduleInfo The module.xp file contents
     * @throws unittest.AssertionFailedError
     */
    protected function assertModule($name, $version, $moduleInfo) {
      ClassLoader::declareModule(newinstance('lang.IClassLoader', array($moduleInfo), '{
        protected $moduleInfo= "";
        public function __construct($moduleInfo) { $this->moduleInfo= $moduleInfo; }
        public function providesResource($name) { return TRUE; }
        public function providesClass($name) { return FALSE; }
        public function providesPackage($name) { return FALSE; }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { return $this->moduleInfo; }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function toString() { return $this->getClassName()."(`".$this->moduleInfo."`)"; }
      }'));
      $this->assertEquals($version, Module::forName($name)->getVersion());
    }

    /**
     * Test a module with a number in its name
     *
     */
    #[@test]
    public function with_number_inside() {
      $this->assertModule('mp3', '2.1.0', '<?php module mp3(2.1.0) { } ?>');
    }

  }
?>
