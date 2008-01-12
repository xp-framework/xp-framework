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
   * TestCase
   *
   * @see      xp://lang.Runtime
   * @purpose  Unittest
   */
  class RuntimeTest extends TestCase {
  
    /**
     * Test getExecutable() method
     *
     */
    #[@test]
    public function getExecutable() {
      $exe= Runtime::getInstance()->getExecutable();
      $this->assertClass($exe, 'lang.Process');
      $this->assertEquals(getmypid(), $exe->getProcessId());
    }

    /**
     * Test extensionAvailable() method
     *
     */
    #[@test]
    public function standardExtensionAvailable() {
      $this->assertTrue(Runtime::getInstance()->extensionAvailable('standard'));
    }

    /**
     * Test extensionAvailable() method
     *
     */
    #[@test]
    public function nonExistantExtension() {
      $this->assertFalse(Runtime::getInstance()->extensionAvailable(':DOES-NOT-EXIST"'));
    }
    
    /**
     * Runs sourcecode in a new runtime
     *
     * @param   string[] args
     * @param   string src
     * @param   int expectedExitCode default 0
     * @throws  lang.IllegalStateException if process exits with a non-zero exitcode
     * @return  string out
     */
    protected function runInNewRuntime($args, $src, $expectedExitCode= 0) {
      $defaultArgs= array(
        '-n',                     // Do not use any configuration file
        '-dsafe_mode=0',          // Switch off "safe" mode
        '-dmagic_quotes_gpc=0',   // Get rid of magic quotes
        '-dextension_dir="'.ini_get('extension_dir').'"',
        '-dinclude_path="'.get_include_path().'"'
      );

      with (
        $out= $err= '', 
        $p= new Process(Runtime::getInstance()->getExecutable()->getFilename(), array_merge($args, $defaultArgs))
      ); {
        $p->in->write('<?php require("lang.base.php"); uses("lang.Runtime"); '.$src.' ?>');
        $p->in->close();

        // Read output
        while ($b= $p->out->read()) { $out.= $b; }
        while ($b= $p->err->read()) { $err.= $b; }

        // Check for exitcode
        if ($expectedExitCode !== ($exitv= $p->close())) {
          throw new IllegalStateException(sprintf(
            "Command %s failed with exit code #%d (instead of %d) {OUT: %s\nERR: %s\n}",
            $p->getCommandLine(),
            $exitv,
            $expectedExitCode,
            $out,
            $err
          ));
        }
      }
      return $out;
    }

    /**
     * Test loadLibrary() method
     *
     */
    #[@test]
    public function loadLoadedLibrary() {
      $this->assertEquals(
        '+OK No exception thrown', 
        $this->runInNewRuntime(array('-denable_dl=1'), '
          try {
            Runtime::getInstance()->loadLibrary("standard");
            echo "+OK No exception thrown";
          } catch (Throwable $e) {
            echo "-ERR ".$e->getClassName();
          }
        ')
      );
    }
      
    /**
     * Test loadLibrary() method
     *
     */
    #[@test]
    public function loadNonExistantLibrary() {
      $this->assertEquals(
        '+OK lang.ElementNotFoundException', 
        $this->runInNewRuntime(array('-denable_dl=1'), '
          try {
            Runtime::getInstance()->loadLibrary(":DOES-NOT-EXIST");
            echo "-ERR No exception thrown";
          } catch (ElementNotFoundException $e) {
            echo "+OK ".$e->getClassName();
          }
        ')
      );
    }

    /**
     * Test loadLibrary() method
     *
     */
    #[@test]
    public function loadLibraryWithoutEnableDl() {
      $this->assertEquals(
        '+OK lang.IllegalAccessException', 
        $this->runInNewRuntime(array('-denable_dl=0'), '
          try {
            Runtime::getInstance()->loadLibrary("irrelevant");
            echo "-ERR No exception thrown";
          } catch (IllegalAccessException $e) {
            echo "+OK ".$e->getClassName();
          }
        ')
      );
    }

    /**
     * Displays information
     *
     */
    #[@test, @ignore('Enable and edit library name to something loadable to see information')]
    public function displayInformation() {
      echo $this->runInNewRuntime(array('-denable_dl=1'), '
        try {
          $r= Runtime::getInstance()->loadLibrary("xsl");
          echo "+OK: ", $r ? "Loaded" : "Compiled";
        } catch (Throwable $e) {
          echo "-ERR ".$e->toString();
        }
      ');
    }

    /**
     * Test addShutdownHook() method
     *
     */
    #[@test]
    public function shutdownHookRunOnScriptEnd() {
      $this->assertEquals(
        '+OK exiting, +OK Shutdown hook run', 
        $this->runInNewRuntime(array('-denable_dl=0'), '
          Runtime::getInstance()->addShutdownHook(newinstance("lang.Runnable", array(), "{
            public function run() {
              echo \'+OK Shutdown hook run\';
            }
          }"));
          
          echo "+OK exiting, ";
        ')
      );
    }

    /**
     * Test addShutdownHook() method
     *
     */
    #[@test]
    public function shutdownHookRunOnNormalExit() {
      $this->assertEquals(
        '+OK exiting, +OK Shutdown hook run', 
        $this->runInNewRuntime(array('-denable_dl=0'), '
          Runtime::getInstance()->addShutdownHook(newinstance("lang.Runnable", array(), "{
            public function run() {
              echo \'+OK Shutdown hook run\';
            }
          }"));
          
          echo "+OK exiting, ";
          exit();
        ')
      );
    }

    /**
     * Test addShutdownHook() method
     *
     */
    #[@test]
    public function shutdownHookRunOnFatal() {
      $out= $this->runInNewRuntime(array('-denable_dl=0'), '
        Runtime::getInstance()->addShutdownHook(newinstance("lang.Runnable", array(), "{
          public function run() {
            echo \'+OK Shutdown hook run\';
          }
        }"));

        echo "+OK exiting";
        $fatal->error();
      ', 255);

      $this->assertEquals('+OK exiting', substr($out, 0, 11), $out);
      $this->assertEquals('+OK Shutdown hook run', substr($out, -21), $out);
    }

    /**
     * Test addShutdownHook() method
     *
     */
    #[@test]
    public function shutdownHookRunOnUncaughtException() {
      $out= $this->runInNewRuntime(array('-denable_dl=0'), '
        Runtime::getInstance()->addShutdownHook(newinstance("lang.Runnable", array(), "{
          public function run() {
            echo \'+OK Shutdown hook run\';
          }
        }"));

        echo "+OK exiting";
        xp::null()->error();
      ', 255);

      $this->assertEquals('+OK exiting', substr($out, 0, 11), $out);
      $this->assertEquals('+OK Shutdown hook run', substr($out, -21), $out);
    }
  }
?>
