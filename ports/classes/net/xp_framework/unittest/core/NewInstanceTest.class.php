<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runnable',
    'lang.Runtime'
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
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance(NULL, NULL)); {
        $p->in->write('<?php require("lang.base.php");');
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
      $this->assertSubclass($o, 'lang.Object');
    }

    /**
     * Test constructing an interface instance
     *
     */
    #[@test]
    public function newRunnable() {
      $o= newinstance('lang.Runnable', array(), '{ public function run() { } }');
      $this->assertSubclass($o, 'lang.Runnable');
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
        (bool)strstr($r[1], 'Fatal error: Class Runnable·'),
        $r[1]
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
        (bool)strstr($r[1], 'Parse error:'),
        $r[1]
      );
    }

    /**
     * Test constructing an interface instance without implementing all
     * required methods raises a fatal
     *
     */
    #[@test]
    public function missingClassFatals() {
      $r= $this->runInNewRuntime(array(), '
        newinstance("lang.Runnable", array(), "{}");
      ');
      $this->assertEquals(255, $r[0], 'exitcode');
      $this->assertTrue(
        (bool)strstr($r[1], 'Class "lang.Runnable" does not exist'),
        $r[1]
      );
    }
  }
?>
