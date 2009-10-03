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
   * @see      reference
   * @purpose  purpose
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
      $this->assertOnStream($this->err, '*** No classloader provides class');
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
      $this->assertOnStream($this->err, '*** File "@@NON-EXISTANT@@.class.php" does not exist!');
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
      $this->assertOnStream($this->err, '*** The file "@@NON-EXISTANT@@.ini" could not be read');
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
  }
?>
