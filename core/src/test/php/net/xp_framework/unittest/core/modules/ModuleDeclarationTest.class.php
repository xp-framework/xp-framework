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
      $m= ClassLoader::declareModule(newinstance('lang.IClassLoader', array($moduleInfo), '{
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
        public function instanceId() { return "(`".$this->moduleInfo."`)"; }
      }'));

      $this->assertEquals($name, $m->getName());
      $this->assertEquals($version, $m->getVersion());
    }

    /**
     * Test a module with only a single character
     *
     */
    #[@test]
    public function single_character() {
      $this->assertModule('a', '2.1.0', '<?php module a(2.1.0) { } ?>');
    }

    /**
     * Test a module with a number in its name
     *
     */
    #[@test]
    public function with_number_inside_name() {
      $this->assertModule('mp3', '2.1.0', '<?php module mp3(2.1.0) { } ?>');
    }

    /**
     * Test a module with "-" in its name
     *
     */
    #[@test]
    public function with_dash_inside_name() {
      $this->assertModule('jenkins-api', '2.1.0', '<?php module jenkins-api(2.1.0) { } ?>');
    }

    /**
     * Test a module with "_" in its name
     *
     */
    #[@test]
    public function with_underscore_inside_name() {
      $this->assertModule('com_dotnet', '2.1.0', '<?php module com_dotnet(2.1.0) { } ?>');
    }

    /**
     * Test a module with "." in its name
     *
     */
    #[@test]
    public function with_dot_inside_name() {
      $this->assertModule('tds.mssql', '2.1.0', '<?php module tds.mssql(2.1.0) { } ?>');
    }

    /**
     * Test a module with "/" in its name
     *
     */
    #[@test]
    public function with_slash_inside_name() {
      $this->assertModule('thekid/dialog', '2.1.0', '<?php module thekid/dialog(2.1.0) { } ?>');
    }

    /**
     * Test a module with a "-" in its version
     *
     */
    #[@test]
    public function alpha_version() {
      $this->assertModule('testalpha', '0.1.0-alpha7', '<?php module testalpha(0.1.0-alpha7) { } ?>');
    }

    /**
     * Test a module with letters in its version
     *
     */
    #[@test]
    public function release_candidate() {
      $this->assertModule('testrc', '5.8.3RC4', '<?php module testrc(5.8.3RC4) { } ?>');
    }

    /**
     * Test a module with letters in its version
     *
     */
    #[@test]
    public function release() {
      $this->assertModule('testrel', '5.9.1', '<?php module testrel(5.9.1) { } ?>');
    }

    /**
     * Negative test: Name may not begin with a number
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function name_may_not_begin_with_number() {
      $this->assertModule(NULL, NULL, '<?php module 3test(2.1.0) { } ?>');
    }

    /**
     * Negative test: Name may not begin with "-"
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function name_may_not_begin_with_dash() {
      $this->assertModule(NULL, NULL, '<?php module -test(2.1.0) { } ?>');
    }

    /**
     * Negative test: Name may not begin with "_"
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function name_may_not_begin_with_underscore() {
      $this->assertModule(NULL, NULL, '<?php module _test(2.1.0) { } ?>');
    }

    /**
     * Negative test: Name may not begin with "."
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function name_may_not_begin_with_dot() {
      $this->assertModule(NULL, NULL, '<?php module .test(2.1.0) { } ?>');
    }

    /**
     * Negative test: Name may not begin with "/"
     *
     */
    #[@test, @expect('lang.ClassFormatException')]
    public function name_may_not_begin_with_slash() {
      $this->assertModule(NULL, NULL, '<?php module /test(2.1.0) { } ?>');
    }

    /**
     * Tests what happens when a module loads a non-existant class via uses()
     *
     */
    #[@test, @expect('lang.ClassDependencyException')]
    public function references_nonexistant_class_via_uses() {
      $this->assertModule(NULL, NULL, '<?php uses("@@non-existant@@"); module broken1(2.1.0) { } ?>');
    }

    /**
     * Tests a missing closing PHP tag doesn't break
     *
     */
    #[@test]
    public function missing_closing_tag() {
      $this->assertModule('no_closing_tag', '2.1.0', '<?php module no_closing_tag(2.1.0) { }');
    }
  }
?>
