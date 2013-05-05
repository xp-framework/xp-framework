<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'lang.Runtime');

  /**
   * TestCase
   *
   * @see  xp://lang.Runtime
   */
  class RuntimeInstantiationTest extends TestCase {

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
     * Runs sourcecode in a new runtime
     *
     * @param   lang.RuntimeOptions options
     * @param   string src
     * @param   int expectedExitCode default 0
     * @throws  lang.IllegalStateException if process exits with a non-zero exitcode
     * @return  string out
     */
    protected function runInNewRuntime(RuntimeOptions $startup, $src, $expectedExitCode= 0) {
      with ($out= $err= '', $p= Runtime::getInstance()->newInstance($startup, 'class', 'xp.runtime.Evaluate', array())); {
        $p->in->write('uses("lang.Runtime");');
        $p->in->write($src);
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
        $this->runInNewRuntime(Runtime::getInstance()->startupOptions()->withSetting('enable_dl', 1), '
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
        $this->runInNewRuntime(Runtime::getInstance()->startupOptions()->withSetting('enable_dl', 1), '
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
        $this->runInNewRuntime(Runtime::getInstance()->startupOptions()->withSetting('enable_dl', 0), '
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
      echo $this->runInNewRuntime(Runtime::getInstance()->startupOptions()->withSetting('enable_dl', 1), '
        try {
          $r= Runtime::getInstance()->loadLibrary("xsl");
          echo "+OK: ", $r ? "Loaded" : "Compiled";
        } catch (Throwable $e) {
          echo "-ERR ".$e->toString();
        }
      ');
    }

    /**
     * Displays information
     *
     */
    #[@test, @ignore('Enable to see information')]
    public function displayCmdLineEnvironment() {
      echo $this->runInNewRuntime(Runtime::getInstance()->startupOptions(), '
        echo getenv("XP_CMDLINE");
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
        $this->runInNewRuntime(Runtime::getInstance()->startupOptions(), '
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
        $this->runInNewRuntime(Runtime::getInstance()->startupOptions(), '
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
      $out= $this->runInNewRuntime(Runtime::getInstance()->startupOptions(), '
        Runtime::getInstance()->addShutdownHook(newinstance("lang.Runnable", array(), "{
          public function run() {
            echo \'+OK Shutdown hook run\';
          }
        }"));

        echo "+OK exiting";
        $fatal= NULL;
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
      $out= $this->runInNewRuntime(Runtime::getInstance()->startupOptions(), '
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