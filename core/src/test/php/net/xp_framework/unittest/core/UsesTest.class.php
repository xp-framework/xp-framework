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
   * TestCase for uses() statement
   *
   */
  class UsesTest extends TestCase {

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
     * Issues a uses() command inside a new runtime for every class given
     * and returns a line indicating success or failure for each of them.
     *
     * @param   string[] uses
     * @param   string decl
     * @return  var[] an array with three elements: exitcode, stdout and stderr contents
     */
    protected function useAllOf($uses, $decl= '') {
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance(NULL, 'class', 'xp.runtime.Evaluate', array())); {
        $p->in->write($decl.'
          ClassLoader::registerPath(\''.strtr($this->getClass()->getClassLoader()->path, '\\', '/').'\');
          $errors= 0;
          foreach (array("'.implode('", "', $uses).'") as $class) {
            try {
              uses($class);
              echo "+OK ", $class, "\n";
            } catch (Throwable $e) {
              echo "-ERR ", $class, ": ", $e->getClassName(), "\n";
              $errors++;
            }
          }
          exit($errors);
        ');
        $p->in->close();

        // Read output
        while ($b= $p->out->read()) { $out.= $b; }
        while ($b= $p->err->read()) { $err.= $b; }

        // Close child process
        $exitv= $p->close();
      }
      return array($exitv, explode("\n", rtrim($out)), explode("\n", rtrim($err)));
    }

    /**
     * Assertion helper
     *
     * @param  int exitv expected exit value
     * @param  string[] out expected STDOUT lines
     * @param  string[] err expected STDERR lines
     * @param  var[] r actualy useAllOf() output
     */
    protected function assertResult($exitv, $out, $err, $r) {
      $this->assertEquals(
        array('exitv' => $exitv, 'out' => $out, 'err' => $err),
        array('exitv' => $r[0], 'out' => $r[1], 'err' => $r[2])
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function useExistingClass() {
      $this->assertResult(
        0, 
        array('+OK '.$this->getClassName()),
        array(''),
        $this->useAllOf(array($this->getClassName()))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function useNonExistantClass() {
      $this->assertResult(
        1, 
        array('-ERR does.not.exist: lang.ClassNotFoundException'),
        array(''),
        $this->useAllOf(array('does.not.exist'))
      );
    }

    /**
     * Test using an existant and one non-existant class
     *
     */
    #[@test]
    public function useClasses() {
      $this->assertResult(
        1, 
        array('+OK '.$this->getClassName(), '-ERR does.not.exist: lang.ClassNotFoundException'),
        array(''),
        $this->useAllOf(array($this->getClassName(), 'does.not.exist'))
      );
    }

    /**
     * Test using a class that has a circular dependency
     *
     * A.class.php
     * <code>
     *   uses('B');
     *
     *   class A extends Object { }
     * </code>
     *
     * B.class.php
     * <code>
     *   uses('C');
     *
     *   class B extends Object { }
     * </code>
     *
     * C.class.php
     * <code>
     *   uses('A');
     *
     *   class C extends Object { }
     * </code>
     *
     */
    #[@test]
    public function circularDependency() {
      $this->assertResult(
        0, 
        array('+OK net.xp_framework.unittest.bootstrap.A'),
        array(''),
        $this->useAllOf(array('net.xp_framework.unittest.bootstrap.A'))
      );
    }

    /**
     * Test using a class that has a circular dependency when
     * ticks are set to 1
     *
     * @see   http://bugs.xp-framework.net/show_bug.cgi?id=19
     */
    #[@test]
    public function circularDependencyWithTicks() {
      $this->assertResult(
        0, 
        array('+OK net.xp_framework.unittest.bootstrap.A'),
        array(''),
        $this->useAllOf(array('net.xp_framework.unittest.bootstrap.A'), 'declare(ticks=1)')
      );
    }
  }
?>
