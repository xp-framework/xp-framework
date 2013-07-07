<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestAction');

  /**
   * Only runs this testcase on a given runtime version, e.g. PHP 5.3.0
   *
   * @see  http://getcomposer.org/doc/01-basic-usage.md#package-versions
   */
  class RuntimeVersion extends Object implements TestAction {
    protected $compare= '';

    /**
     * Create a new RuntimeVersion match
     *
     * @param string pattern A pattern to match against PHP_VERSION
     */
    public function __construct($pattern) {
      $cmp= array();
      foreach (explode(',', $pattern) as $specifier) {
        if ('*' === $specifier{strlen($specifier)- 1}) {
          $cmp[]= function($compare) use($specifier) { return 0 === strncmp($compare, $specifier, strlen($specifier)- 1); };
        } else if ('~' === $specifier{0}) {
          sscanf($specifier, '~%d.%d.%d', $s, $m, $p);
          $lower= sprintf('%d.%d.%d', $s, $m, $p);
          $upper= sprintf('%d.%d.0', $s, $m + 1);
          $cmp[]= function($compare) use($lower, $upper) {
            return version_compare($compare, $lower, 'ge') && version_compare($compare, $upper, 'lt');
          };
        } else if ('<' === $specifier{0}) {
          if ('=' === $specifier{1}) {
            $op= 'le';
            $specifier= substr($specifier, 2);
          } else {
            $op= 'lt';
            $specifier= substr($specifier, 1);
          }
          $cmp[]= function($compare) use($specifier, $op) { return version_compare($compare, $specifier, $op); };
        } else if ('>' === $specifier{0}) {
          if ('=' === $specifier{1}) {
            $op= 'ge';
            $specifier= substr($specifier, 2);
          } else {
            $op= 'gt';
            $specifier= substr($specifier, 1);
          }
          $cmp[]= function($compare) use($specifier, $op) { return version_compare($compare, $specifier, $op); };
        } else if ('!=' === $specifier{0}.$specifier{1}) {
          $cmp[]= function($compare) use($specifier) { return $compare !== substr($specifier, 2); };
        } else {
          $cmp[]= function($compare) use($specifier) { return $compare === $specifier; };
        }
      }

      $this->compare= function($compare) use($cmp) {
        foreach ($cmp as $f) {
          if (!$f($compare)) return FALSE;
        }
        return TRUE;
      };
    }

    /**
     * Verify a given OS matches this platform
     *
     * @param  string os The OS' name - omit to use current OS
     * @return bool
     */
    public function verify($version= NULL) {
      $version ?: $version= PHP_VERSION;
      return call_user_func($this->compare, $version);
    }

    /**
     * This method gets invoked before a test method is invoked, and before
     * the setUp() method is called.
     *
     * @param  unittest.TestCase $t
     * @throws unittest.PrerequisitesNotMetError
     */
    public function beforeTest(TestCase $t) { 
      if (!$this->verify()) {
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