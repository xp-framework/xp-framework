<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use unittest\actions\RuntimeVersion;
use lang\Runtime;

/**
 * Testcase ensuring XP refuses to start when magic_quotes_gpc is enabled.
 *
 * @see   http://svn.php.net/viewvc/php/php-src/branches/PHP_5_4/NEWS?revision=318433&view=markup
 * @see   php://magic_quotes
 */
class MagicQuotesTest extends TestCase {

  /**
   * Skips tests if process execution has been disabled.
   */
  #[@beforeClass]
  public static function verifyProcessExecutionEnabled() {
    if (\lang\Process::$DISABLED) {
      throw new \unittest\PrerequisitesNotMetError('Process execution disabled', NULL, array('enabled'));
    }
  }

  /**
   * Create a new runtime
   *
   * @return  var[] an array with three elements: exitcode, stdout and stderr contents
   */
  protected function run() {
    $runtime= Runtime::getInstance();
    $options= $runtime->startupOptions()->withSetting('magic_quotes_gpc', 1)->withSetting('error_reporting', 'E_ALL');
    $out= $err= '';

    with ($p= $runtime->newInstance($options, 'class', 'xp.runtime.Evaluate', array('return 1;'))); {
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
   * Before PHP 5.3, the XP Framework is the only one that has a problem
   * with magic quotes.
   */
  #[@test, @action(new RuntimeVersion('<5.3.0'))]
  public function xpFrameworkRefusesToStart() {
    $r= $this->run();
    $this->assertEquals(255, $r[0], 'exitcode');
    $this->assertTrue((bool)strstr($r[1], "[xp::core] magic_quotes_gpc enabled"), \xp::stringOf($r));
  }

  /**
   * As of PHP 5.3+, magic_quotes_gpc = On raises a deprecation warning 
   * to standard error. 
   */
  #[@test, @action(new RuntimeVersion('>=5.3.4,<5.4.0'))]
  public function xpFrameworkRefusesToStartAndDeprecationWarning() {
    $r= $this->run();
    $this->assertEquals(255, $r[0], 'exitcode');
    $this->assertTrue((bool)strstr($r[1], "[xp::core] magic_quotes_gpc enabled"), \xp::stringOf($r));
    $this->assertTrue((bool)strstr($r[2], "Directive 'magic_quotes_gpc' is deprecated in PHP 5.3 and greater"), \xp::stringOf($r));
  }
  
  /**
   * As of PHP 5.4, magic quotes have been removed and enabling them will
   * cause PHP to issue a fatal error.
   */
  #[@test, @action(new RuntimeVersion('>=5.4.0'))]
  public function phpRefusesToStart() {
    $r= $this->run();
    $this->assertEquals(1, $r[0], 'exitcode');
    $this->assertTrue((bool)strstr($r[1].$r[2], "Directive 'magic_quotes_gpc' is no longer available in PHP"), \xp::stringOf($r));
  }
}
