<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   */
  class BootstrapTest extends TestCase {
    protected $includePath;
    
    /**
     * Saves include_path
     *
     */
    public function setUp() {
      $this->includePath= get_include_path();
    }
    
    /**
     * Restores include_path
     *
     */
    public function tearDown() {
      set_include_path($this->includePath);
    }
  
    /**
     * Create a new runtime
     *
     * @param   string[] uses
     * @param   string decl
     * @return  var[] an array with three elements: exitcode, stdout and stderr contents
     */
    protected function runWith(RuntimeOptions $options) {
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance($options, NULL)); {
        $p->in->write('<?php require("lang.base.php"); ?>');
        $p->in->close();

        // Read output
        while ($b= $p->out->read()) { $out.= $b; }
        while ($b= $p->err->read()) { $err.= $b; }

        // Close child process
        $exitv= $p->close();
      }
      return array($exitv, $out, $err);
    }

    /**
     * Test non-existant classpath elements raise a fatal error
     *
     */
    #[@test]
    public function fatalsForMagicQuotesGPC() {
      $r= $this->runWith(Runtime::getInstance()->startupOptions()->withSetting('magic_quotes_gpc', 1));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], '[xp::core] magic_quotes_gpc enabled'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );

      // In PHP 5.3+, magic_quotes_gpc = On raises a "PHP Warning:  Directive 
      // 'magic_quotes_gpc' is deprecated in PHP 5.3 and greater" to standard
      // error. This cannot be suppressed by display_startup_errors = Off, so
      // not checking STDERR at all.
    }
  
    /**
     * Test non-existant classpath elements raise a fatal error
     *
     */
    #[@test]
    public function fatalsForNonExistingPaths() {
      set_include_path($this->includePath.PATH_SEPARATOR.'/does-not-exist');
      $r= $this->runWith(Runtime::getInstance()->startupOptions());
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], '[bootstrap] Classpath element [/does-not-exist] not found'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }

    /**
     * Test non-existant classpath elements raise a fatal error
     *
     */
    #[@test]
    public function fatalsForNonExistingXars() {
      set_include_path($this->includePath.PATH_SEPARATOR.'/does-not-exist.xar');
      $r= $this->runWith(Runtime::getInstance()->startupOptions());
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], '[bootstrap] Classpath element [/does-not-exist.xar] not found'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }
  }
?>
