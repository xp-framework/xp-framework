<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestAction');

  /**
   * Only runs this testcase on a given runtime version, e.g. PHP 5.3.0
   *
   * @test xp://net.xp_framework.unittest.tests.RuntimeVersionTest
   * @see  http://getcomposer.org/doc/01-basic-usage.md#package-versions
   */
  class RuntimeVersion extends Object implements TestAction {
    protected $compare= array();

    /**
     * Create a new RuntimeVersion match
     *
     * @param string pattern A pattern to match against PHP_VERSION
     */
    public function __construct($pattern) {
      foreach (explode(',', $pattern) as $specifier) {
        if ('*' === $specifier{strlen($specifier)- 1}) {
          $this->compare[]= function($compare) use($specifier) {
            return 0 === strncmp($compare, $specifier, strlen($specifier)- 1);
          };
        } else if ('~' === $specifier{0}) {
          $c= sscanf($specifier, '~%d.%d.%d', $s, $m, $p);
          $lower= substr($specifier, 1);
          switch ($c) {
            case 2: $upper= sprintf('%d.0.0', $s + 1); break;
            case 3: $upper= sprintf('%d.%d.0', $s, $m + 1); break;
          }
          $this->compare[]= function($compare) use($lower, $upper) {
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
          $this->compare[]= function($compare) use($specifier, $op) {
            return version_compare($compare, $specifier, $op);
          };
        } else if ('>' === $specifier{0}) {
          if ('=' === $specifier{1}) {
            $op= 'ge';
            $specifier= substr($specifier, 2);
          } else {
            $op= 'gt';
            $specifier= substr($specifier, 1);
          }
          $this->compare[]= function($compare) use($specifier, $op) {
            return version_compare($compare, $specifier, $op);
          };
        } else if ('!=' === $specifier{0}.$specifier{1}) {
          $this->compare[]= function($compare) use($specifier) {
            return $compare !== substr($specifier, 2);
          };
        } else {
          $this->compare[]= function($compare) use($specifier) {
            return $compare === $specifier;
          };
        }
      }
    }

    /**
     * Verify a given runtime version matches this constraint
     *
     * @param  string version The runtime's version - omit to use current version
     * @return bool
     */
    public function verify($version= NULL) {
      $version ?: $version= PHP_VERSION;
      foreach ($this->compare as $f) {
        if (!$f($version)) return FALSE;
      }
      return TRUE;
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
        $compare= '';
        foreach ($this->compare as $f) {
          $test= '';
          $reflect= new ReflectionFunction($f);   // TODO: Closure reflection via XP
          foreach ($reflect->getStaticVariables() as $name => $value) {
            $test.= ', '.$name.'= '.var_export($value, TRUE);
          }
          $compare.= ' && ('.substr($test, 2).')';
        }
        throw new PrerequisitesNotMetError('Test not intended for this version ('.PHP_VERSION.')', NULL, array(substr($compare, 4)));
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