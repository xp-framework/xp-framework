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
    protected $startupOptions= NULL;

    /**
     * Initialize startup options
     *
     */
    public function setUp() {
      $this->startupOptions= Runtime::getInstance()->startupOptions()
        ->withSwitch('n')               // Do not use any configuration file
        ->withSetting('include_path', '.'.PATH_SEPARATOR.get_include_path())
      ;
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
      with (
        $out= $err= '', 
        $rt= Runtime::getInstance(),
        $p= $rt->getExecutable()->newInstance($this->startupOptions->asArguments())
      ); {
        $p->in->write('<?php '.$decl.'
          require("lang.base.php"); 
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
        ?>');
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
     * Test
     *
     */
    #[@test]
    public function useExistingClass() {
      $r= $this->useAllOf(array($this->getClassName()));
      $this->assertEquals(0, $r[0], 'exitcode');
      $this->assertEquals(array('+OK '.$this->getClassName()), $r[1]);
      $this->assertEquals(array(''), $r[2]);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function useNonExistantClass() {
      $r= $this->useAllOf(array('does.not.exist'));
      $this->assertEquals(1, $r[0], 'exitcode');
      $this->assertEquals(array('-ERR does.not.exist: lang.ClassNotFoundException'), $r[1]);
      $this->assertEquals(array(''), $r[2]);
    }

    /**
     * Test using an existant and one non-existant class
     *
     */
    #[@test]
    public function useClasses() {
      $r= $this->useAllOf(array($this->getClassName(), 'does.not.exist'));
      $this->assertEquals(1, $r[0], 'exitcode');
      $this->assertEquals(
        array('+OK '.$this->getClassName(), '-ERR does.not.exist: lang.ClassNotFoundException'),
        $r[1]
      );
      $this->assertEquals(array(''), $r[2]);
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
      $r= $this->useAllOf(array('net.xp_framework.unittest.bootstrap.A'));
      $this->assertEquals(0, $r[0], 'exitcode');
      $this->assertEquals(array('+OK net.xp_framework.unittest.bootstrap.A'), $r[1]);
      $this->assertEquals(array(''), $r[2]);
    }

    /**
     * Test using a class that has a circular dependency when
     * ticks are set to 1
     *
     * @see   http://bugs.xp-framework.net/show_bug.cgi?id=19
     */
    #[@test]
    public function circularDependencyWithTicks() {
      $r= $this->useAllOf(array('net.xp_framework.unittest.bootstrap.A'), 'declare(ticks=1)');
      $this->assertEquals(0, $r[0], 'exitcode');
      $this->assertEquals(array('+OK net.xp_framework.unittest.bootstrap.A'), $r[1]);
      $this->assertEquals(array(''), $r[2]);
    }
  }
?>
