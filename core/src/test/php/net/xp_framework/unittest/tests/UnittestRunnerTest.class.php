<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.unittest.Runner',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see  xp://xp.unittest.Runner
   */
  class UnittestRunnerTest extends TestCase {
    protected
      $runner = NULL,
      $out    = NULL,
      $err    = NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->runner= new xp·unittest·Runner();
      $this->out= $this->runner->setOut(new MemoryOutputStream());
      $this->err= $this->runner->setErr(new MemoryOutputStream());
    }

    /**
     * Asserts a given output stream contains the given bytes       
     *
     * @param   io.streams.MemoryOutputStream m
     * @param   string bytes
     * @throws  unittest.AssertionFailedError
     */
    protected function assertOnStream(MemoryOutputStream $m, $bytes, $message= 'Not contained') {
      strstr($m->getBytes(), $bytes) || $this->fail($message, $m->getBytes(), $bytes);
    }
  
    /**
     * Test self usage - that is, when unittest is invoked without any 
     * arguments
     *
     */
    #[@test]
    public function selfUsage() {
      $return= $this->runner->run(array());
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, 'Usage:');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test usage is displayed when "-?" is passed
     *
     */
    #[@test]
    public function helpParameter() {
      $return= $this->runner->run(array('-?'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, 'Usage:');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with no tests
     *
     */
    #[@test]
    public function noTests() {
      $return= $this->runner->run(array('-v'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** No tests specified');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant class
     *
     */
    #[@test]
    public function nonExistantClass() {
      $return= $this->runner->run(array('@@NON-EXISTANT@@'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** Class "@@NON-EXISTANT@@" could not be found');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant file
     *
     */
    #[@test]
    public function nonExistantFile() {
      $return= $this->runner->run(array('@@NON-EXISTANT@@'.xp::CLASS_FILE_EXT));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '@@NON-EXISTANT@@.class.php" does not exist!');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant package
     *
     */
    #[@test]
    public function nonExistantPackage() {
      $return= $this->runner->run(array('@@NON-EXISTANT@@.*'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** No classloaders provide @@NON-EXISTANT@@');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant package
     *
     */
    #[@test]
    public function nonExistantPackageRecursive() {
      $return= $this->runner->run(array('@@NON-EXISTANT@@.**'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** No classloaders provide @@NON-EXISTANT@@');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test when invoked with a non-existant properties
     *
     */
    #[@test]
    public function nonExistantProperties() {
      $return= $this->runner->run(array('@@NON-EXISTANT@@.ini'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** File "@@NON-EXISTANT@@.ini" not found');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test running a test class without tests inside
     *
     */
    #[@test]
    public function runEmptyTest() {
      $command= newinstance('unittest.TestCase', array($this->name), '{
      }');
      $return= $this->runner->run(array($command->getClassName()));
      $this->assertEquals(3, $return);
      $this->assertOnStream($this->err, '*** Warning: No tests found in');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test running a class that is not a test case
     *
     */
    #[@test]
    public function runNonTest() {
      $return= $this->runner->run(array('lang.Object'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** Error: Given argument is not a TestCase class (lang.XPClass<lang.Object>)');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test running a succeeding test
     *
     */
    #[@test]
    public function runSucceedingTest() {
      $command= newinstance('unittest.TestCase', array('succeeds'), '{
        #[@test]
        public function succeeds() {
          $this->assertTrue(TRUE);
        }
      }');
      $return= $this->runner->run(array($command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertOnStream($this->out, 'OK: 1/1 run (0 skipped), 1 succeeded, 0 failed');
    }

    /**
     * Test running a colored test
     *
     */
    #[@test]
    public function runColoredTest($setting= '--color=on') {
      $command= newinstance('unittest.TestCase', array('succeeds'), '{
        #[@test]
        public function succeeds() {
          $this->assertTrue(TRUE);
        }
      }');
      $return= $this->runner->run(array($setting, $command->getClassName()));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertOnStream($this->out, 'OK: 1/1 run (0 skipped), 1 succeeded, 0 failed');
    }

    /**
     * Test running a noncolored test
     *
     */
    #[@test]
    public function runNocolorTest() {
      $this->runColoredTest('--color=off');
    }

    /**
     * Test running a noncolored test
     *
     */
    #[@test]
    public function runAutocolorTest() {
      $this->runColoredTest('--color=auto');
    }


    /**
     * Test running a noncolored test
     *
     */
    #[@test]
    public function runShortAutocolorTest() {
      $this->runColoredTest('--color');
    }

    /**
     * Test running a noncolored test
     *
     */
    #[@test]
    public function runUnsupportedColorSettingTestFails() {
      $command= newinstance('unittest.TestCase', array('succeeds'), '{
        #[@test]
        public function succeeds() {
          $this->assertTrue(TRUE);
        }
      }');
      $return= $this->runner->run(array('--color=anything', $command->getClassName()));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** Unsupported argument for --color');
    }

    /**
     * Test running a failing test
     *
     */
    #[@test]
    public function runFailingTest() {
      $command= newinstance('unittest.TestCase', array('fails'), '{
        #[@test]
        public function fails() {
          $this->assertTrue(FALSE);
        }
      }');
      $return= $this->runner->run(array($command->getClassName()));
      $this->assertEquals(1, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertOnStream($this->out, 'FAIL: 1/1 run (0 skipped), 0 succeeded, 1 failed');
    }

    /**
     * Test "-e"(val) option with missing source
     *
     */
    #[@test]
    public function evaluateWithoutSource() {
      $return= $this->runner->run(array('-e'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** Option -e requires an argument');
      $this->assertEquals('', $this->out->getBytes());
    }

    /**
     * Test running a succeeding test with "-e"(val) option
     *
     */
    #[@test]
    public function evaluateSucceedingTest() {
      $return= $this->runner->run(array('-e', '$this->assertTrue(TRUE);'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertOnStream($this->out, 'OK: 1/1 run (0 skipped), 1 succeeded, 0 failed');
    }

    /**
     * Test running a failing test with "-e"(val) option
     *
     */
    #[@test]
    public function evaluateFailingTest() {
      $return= $this->runner->run(array('-e', '$this->assertTrue(FALSE);'));
      $this->assertEquals(1, $return);
      $this->assertEquals('', $this->err->getBytes());
      $this->assertOnStream($this->out, 'FAIL: 1/1 run (0 skipped), 0 succeeded, 1 failed');
    }

    /**
     * Test running a single test
     *
     */
    #[@test]
    public function runSingleTest() {
      $command= newinstance('unittest.TestCase', array('succeeds'), '{
        #[@test]
        public function succeeds() {
          $this->assertTrue(TRUE);
        }
      }');
      $return= $this->runner->run(array($command->getClassName().'::succeeds'));
      $this->assertEquals(0, $return);
      $this->assertEquals('', $this->err->getBytes());
    }

    /**
     * Test running a single test
     *
     */
    #[@test]
    public function runSingleTestWrongSpec() {
      $command= newinstance('unittest.TestCase', array('succeeds'), '{
        #[@test]
        public function succeeds() {
          $this->assertTrue(TRUE);
        }
      }');
      $return= $this->runner->run(array($command->getClassName().'::succeed'));
      $this->assertEquals(1, $return);
      $this->assertOnStream($this->err, '*** Error: Test method does not exist: succeed()');
    }

    /**
     * Test running a single test
     *
     */
    #[@test]
    public function withListener() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithListenerTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-'));
      $this->assertEquals(
        array(), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test listener options
     *
     */
    #[@test]
    public function withListenerOptions() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithListenerOptionsTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
        #[@arg]
        public function setVerbose() { self::$options[__FUNCTION__]= TRUE; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'option', 'value', '-o', 'v'));
      $this->assertEquals(
        array('setOption' => 'value', 'setVerbose' => TRUE), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test long listener option
     *
     */
    #[@test]
    public function withLongListenerOption() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithLongListenerOptionTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'option', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test long listener option
     *
     */
    #[@test]
    public function withNamedLongListenerOption() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithNamedLongListenerOptionTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg(name = "use")]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'use', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test long listener option
     *
     */
    #[@test]
    public function withNamedLongListenerOptionShort() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithNamedLongListenerOptionShortTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg(name = "use")]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'u', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }    

    /**
     * Test short listener option
     *
     */
    #[@test]
    public function withShortListenerOption() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithShortListenerOptionTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'o', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test short listener option
     *
     */
    #[@test]
    public function withNamedShortListenerOption() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithNamedShortListenerOptionTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg(short = "O")]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'O', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }

    /**
     * Test short listener option
     *
     */
    #[@test]
    public function withPositionalOptionListenerOption() {
      $class= ClassLoader::getDefault()->defineClass('net.xp_framework.unittest.tests.WithPositionalOptionTestFixture', 'xp.unittest.DefaultListener', array(), '{
        public static $options= array();
        #[@arg(position= 0)]
        public function setOption($value) { self::$options[__FUNCTION__]= $value; }
      }');

      $return= $this->runner->run(array('-l', $class->getName(), '-', '-o', 'value'));
      $this->assertEquals(
        array('setOption' => 'value'), 
        $class->getField('options')->get(NULL)
      );
    }

  }
?>
