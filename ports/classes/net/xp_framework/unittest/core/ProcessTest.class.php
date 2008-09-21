<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.Runtime',
    'lang.System',
    'lang.Process',
    'io.streams.Streams',
    'io.streams.MemoryOutputStream'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.Process
   * @purpose  Unittest
   */
  class ProcessTest extends TestCase {
  
    /**
     * Return executable name
     *
     * @return  string
     */
    protected function executable() {
      return Runtime::getInstance()->getExecutable()->getFilename();
    }
  
    /**
     * Test process status information methods
     *
     * @see      xp://lang.Process#getProcessId
     * @see      xp://lang.Process#getFilename
     * @see      xp://lang.Process#getCommandLine
     * @see      xp://lang.Process#exitValue
     */
    #[@test]
    public function information() {
      $p= new Process($this->executable(), array('-v'));
      try {
        $this->assertEquals(-1, $p->exitValue(), 'Process should not have exited yet');
        $this->assertNotEquals(0, $p->getProcessId());
        $this->assertNotEquals('', $p->getFilename());
        $this->assertTrue(create(new String($p->getCommandLine()))->contains('-v'));
        $p->close();
      } catch (AssertionFailedError $e) {
        $p->close();    // Ensure process is closed
        throw $e;
      }
    }
    
    /**
     * Tests Process::newInstance()
     *
     */
    #[@test]
    public function newInstance() {
      $p= Runtime::getInstance()->getExecutable()->newInstance(array('-v'));
      $version= 'PHP '.phpversion();
      $this->assertEquals($version, $p->out->read(strlen($version)));
      $p->close();
    }

    /**
     * Test exit value
     *
     */
    #[@test]
    public function exitValue() {
      $p= new Process($this->executable(), array('-r', escapeshellarg('exit(0);')));
      $this->assertEquals(0, $p->close());
    }

    /**
     * Test non-zero exit value
     *
     */
    #[@test]
    public function nonZeroExitValue() {
      $p= new Process($this->executable(), array('-r', escapeshellarg('exit(2);')));
      $this->assertEquals(2, $p->close());
    }

    /**
     * Test standard input
     *
     */
    #[@test]
    public function stdIn() {
      $p= new Process($this->executable(), array('-r', escapeshellarg('fprintf(STDOUT, fread(STDIN, 0xFF));')));
      try {
        $p->in->write('IN');
        $p->in->close();
        $this->assertEquals('IN', $p->out->read());
      } catch (AssertionFailedError $e) {
        $p->close();    // Ensure process is closed
        throw $e;
      }
    }

    /**
     * Test standard output
     *
     */
    #[@test]
    public function stdOut() {
      $p= new Process($this->executable(), array('-r', escapeshellarg('fprintf(STDOUT, "OUT");')));
      try {
        $this->assertEquals('OUT', $p->out->read());
      } catch (AssertionFailedError $e) {
        $p->close();    // Ensure process is closed
        throw $e;
      }
    }

    /**
     * Test standard error
     *
     */
    #[@test]
    public function stdErr() {
      $p= new Process($this->executable(), array('-r', escapeshellarg('fprintf(STDERR, "ERR");')));
      try {
        $this->assertEquals('ERR', $p->err->read());
      } catch (AssertionFailedError $e) {
        $p->close();    // Ensure process is closed
        throw $e;
      }
    }

    /**
     * Test running a non-existant file
     *
     */
    #[@test, @expect('io.IOException')]
    public function runningNonExistantFile() {
      new Process(':FILE_DOES_NOT_EXIST:');
    }

    /**
     * Test running a directory (System::tempDir() used as argument)
     *
     */
    #[@test, @expect('io.IOException')]
    public function runningDirectory() {
      new Process(System::tempDir());
    }
  }
?>
