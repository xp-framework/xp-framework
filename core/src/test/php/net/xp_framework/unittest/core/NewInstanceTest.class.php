<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runnable',
    'lang.Runtime',
    'net.xp_framework.unittest.core.PackagedClass',
    'net.xp_framework.unittest.core.NamespacedClass',
    'net.xp_framework.unittest.core.NamespacedInterface'
  );

  /**
   * TestCase for newinstance() functionality
   *
   */
  class NewInstanceTest extends TestCase {

    /**
     * Issues a uses() command inside a new runtime for every class given
     * and returns a line indicating success or failure for each of them.
     *
     * @param   string[] uses
     * @param   string src
     * @return  var[] an array with three elements: exitcode, stdout and stderr contents
     */
    protected function runInNewRuntime($uses, $src) {
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance(NULL, 'class', 'xp.runtime.Evaluate', array())); {
        $uses && $p->in->write('uses("'.implode('", "', $uses).'");');
        $p->in->write($src);
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
     * Test constructing a class instance
     *
     */
    #[@test]
    public function newObject() {
      $o= newinstance('lang.Object', array(), '{}');
      $this->assertInstanceOf('lang.Object', $o);
    }

    /**
     * Test constructing an interface instance
     *
     */
    #[@test]
    public function newRunnable() {
      $o= newinstance('lang.Runnable', array(), '{ public function run() { } }');
      $this->assertInstanceOf('lang.Runnable', $o);
    }

    /**
     * Test arguments are passed constructor
     *
     */
    #[@test]
    public function argumentsArePassedToConstructor() {
      $instance= newinstance('lang.Object', array($this), '{
        public $test= NULL;
        public function __construct($test) {
          $this->test= $test;
        }
      }');
      $this->assertEquals($this, $instance->test);
    }

    /**
     * Test constructing an interface instance without implementing all
     * required methods raises a fatal
     *
     */
    #[@test]
    public function missingMethodImplementationFatals() {
      $r= $this->runInNewRuntime(array('lang.Runnable'), '
        newinstance("lang.Runnable", array(), "{}");
      ');
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], 'Fatal error:'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }

    /**
     * Test invalid syntax raises a fatal.
     *
     */
    #[@test]
    public function syntaxErrorFatals() {
      $r= $this->runInNewRuntime(array('lang.Runnable'), '
        newinstance("lang.Runnable", array(), "{ @__SYNTAX ERROR__@ }");
      ');
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], 'Parse error:'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }

    /**
     * Test using a non-existant class in newinstance() causes a fatal error.
     *
     */
    #[@test]
    public function missingClassFatals() {
      $r= $this->runInNewRuntime(array(), '
        newinstance("lang.NonExistantClass", array(), "{}");
      ');
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], 'Class "lang.NonExistantClass" could not be found'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }

    /**
     * Test using a not previously defined class is loaded
     *
     */
    #[@test]
    public function notPreviouslyDefinedClassIsLoaded() {
      $r= $this->runInNewRuntime(array(), '
        if (isset(xp::$cl["lang.Runnable"])) {
          xp::error("Class lang.Runnable may not have been previously loaded");
        }
        $r= newinstance("lang.Runnable", array(), "{ public function run() { echo \"Hi\"; } }");
        $r->run();
      ');
      $this->assertEquals(0, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1].$r[2], 'Hi'),
        xp::stringOf(array('out' => $r[1], 'err' => $r[2]))
      );
    }

    /**
     * Tests package retrieval on newinstance() created namespaced class
     *
     */
    #[@test]
    public function packageOfNewInstancedClass() {
      $i= newinstance('lang.Object', array(), '{}');
      $this->assertEquals(
        Package::forName('lang'),
        $i->getClass()->getPackage()
      );
    }

    /**
     * Tests package retrieval on newinstance() created namespaced class
     *
     */
    #[@test]
    public function packageOfNewInstancedFullyQualifiedClass() {
      $i= newinstance('net.xp_framework.unittest.core.PackagedClass', array(), '{}');
      $this->assertEquals(
        Package::forName('net.xp_framework.unittest.core'),
        $i->getClass()->getPackage()
      );
    }

    /**
     * Tests package retrieval on newinstance() created namespaced class
     *
     */
    #[@test]
    public function packageOfNewInstancedNamespacedClass() {
      $i= newinstance('net.xp_framework.unittest.core.NamespacedClass', array(), '{}');
      $this->assertEquals(
        Package::forName('net.xp_framework.unittest.core'),
        $i->getClass()->getPackage()
      );
    }

    /**
     * Tests package retrieval on newinstance() created namespaced class
     *
     */
    #[@test]
    public function packageOfNewInstancedNamespacedInterface() {
      $i= newinstance('net.xp_framework.unittest.core.NamespacedInterface', array(), '{}');
      $this->assertEquals(
        Package::forName('net.xp_framework.unittest.core'),
        $i->getClass()->getPackage()
      );
    }

    /**
     * Test class name of a anonymous generic instance
     *
     */
    #[@test]
    public function className() {
      $instance= newinstance('Object', array(), '{ }');
      $n= $instance->getClassName();
      $this->assertEquals(
        'lang.Object',
        substr($n, 0, strrpos($n, '·')),
        $n
      );
    }

    /**
     * Test class name of a anonymous generic instance
     *
     */
    #[@test]
    public function classNameWithFullyQualifiedClassName() {
      $instance= newinstance('lang.Object', array(), '{ }');
      $n= $instance->getClassName();
      $this->assertEquals(
        'lang.Object',
        substr($n, 0, strrpos($n, '·')),
        $n
      );
    }
  }
?>
