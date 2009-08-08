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
    protected $startupOptions= NULL;

    /**
     * Initialize startup options
     *
     */
    public function setUp() {
      $this->startupOptions= Runtime::getInstance()->startupOptions();
    }
  
    /**
     * Create a new runtime
     *
     * @param   string[] uses
     * @param   string decl
     * @return  var[] an array with three elements: exitcode, stdout and stderr contents
     */
    protected function runWith(RuntimeOptions $options) {
      with (
        $out= $err= '', 
        $p= Runtime::getInstance()->getExecutable()->newInstance($options->asArguments())
      ); {
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
      $r= $this->runWith($this->startupOptions->withSetting('magic_quotes_gpc', 1));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1], 'Fatal error: [xp::core] magic_quotes_gpc enabled'),
        $r[1]
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
      $r= $this->runWith($this->startupOptions->withSetting('include_path', implode(
        PATH_SEPARATOR, 
        array_merge($this->startupOptions->getSetting('include_path'), array('/does-not-exist'))
      )));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1], 'Fatal error: [bootstrap] Classpath element [/does-not-exist] not found'),
        $r[1]
      );
      $this->assertEquals('', $r[2]);
    }

    /**
     * Test non-existant classpath elements raise a fatal error
     *
     */
    #[@test]
    public function fatalsForNonExistingXars() {
      $r= $this->runWith($this->startupOptions->withSetting('include_path', implode(
        PATH_SEPARATOR, 
        array_merge($this->startupOptions->getSetting('include_path'), array('/does-not-exist.xar'))
      )));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1], 'Fatal error: [bootstrap] Classpath element [/does-not-exist.xar] not found'),
        $r[1]
      );
      $this->assertEquals('', $r[2]);
    }
  }
?>
