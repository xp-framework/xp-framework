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

    /**
     * Skips tests if process execution has been disabled.
     */
    #[@beforeClass]
    public static function verifyProcessExecutionEnabled() {
      if (Process::$DISABLED) {
        throw new PrerequisitesNotMetError('Process execution disabled', NULL, array('enabled'));
      }
    }
  
    /**
     * Create a new runtime
     *
     * @param   string[] uses
     * @param   string decl
     * @return  var[] an array with three elements: exitcode, stdout and stderr contents
     */
    protected function runWith(RuntimeOptions $options) {
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance($options, 'class', 'xp.runtime.Evaluate', array('return 1;'))); {
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
     * Helper to run bootstrapping with given tz
     *
     * @param   string tz
     */
    protected function runWithTz($tz) {
      $r= $this->runWith(Runtime::getInstance()->startupOptions()->withSetting('date.timezone', $tz));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], '[xp::core] date.timezone not configured properly.'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }    
    
    /**
     * Test XP fatals when no timezone is set
     *
     */
    #[@test]
    public function fatalsForEmptyTimezone() {
      $this->runWithTz('');
    }
    
    /**
     * Test XP fatals when invalid timezone is set
     *
     */
    #[@test]
    public function fatalsForInvalidTimezone() {
      $this->runWithTz('Foo/bar');
    }
    
    /**
     * Test non-existant classpath elements raise a fatal error
     *
     */
    #[@test]
    public function fatalsForNonExistingPaths() {
      $r= $this->runWith(Runtime::getInstance()->startupOptions()->withClassPath('/does-not-exist'));
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
      $r= $this->runWith(Runtime::getInstance()->startupOptions()->withClassPath('/does-not-exist.xar'));
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], '[bootstrap] Classpath element [/does-not-exist.xar] not found'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }
  }
?>
