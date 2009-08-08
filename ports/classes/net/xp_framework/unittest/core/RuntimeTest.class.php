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
    protected $startupOptions= array();
    protected static $runtimeExecutable= NULL;
    
    /**
     * Determine runtime
     *
     */
    #[@beforeClass]
    public static function determineRuntime() {
      self::$runtimeExecutable= Runtime::getInstance()->getExecutable();
    }
    
    /**
     * Initialize startup options
     *
     */
    public function setUp() {
      $this->startupOptions= Runtime::getInstance()->startupOptions()
        ->withSwitch('n')               // Do not use any configuration file
        ->withSetting('safe_mode', 0)   // Switch off "safe" mode
      ;
    }
    
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
     * Test startupOptions() method
     *
     */
    #[@test]
    public function startupOptions() {
      $startup= Runtime::getInstance()->startupOptions();
      $this->assertClass($startup, 'lang.RuntimeOptions');
    }

    /**
     * Test bootstrapScript() method
     *
     */
    #[@test]
    public function bootstrapScript() {
      $bootstrap= Runtime::getInstance()->bootstrapScript();
      $this->assertTrue(strstr($bootstrap, 'tools') && strstr($bootstrap, '.php'), $bootstrap);
    }

    /**
     * Test mainClass() method
     *
     */
    #[@test]
    public function mainClass() {
      $main= Runtime::getInstance()->mainClass();
      $this->assertClass($main, 'lang.XPClass');
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSetting() {
      $startup= Runtime::parseArguments(array('-denable_dl=0'));
      $this->assertEquals(array('0'), $startup['options']->getSetting('enable_dl'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSettingToleratesWhitespace() {
      $startup= Runtime::parseArguments(array('-d magic_quotes_gpc=0'));
      $this->assertEquals(array('0'), $startup['options']->getSetting('magic_quotes_gpc'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function doubleDashEndsOptions() {
      $startup= Runtime::parseArguments(array('-q', '--', 'tools/xar.php'));
      $this->assertEquals(array('-q'), $startup['options']->asArguments());
      $this->assertEquals('tools/xar.php', $startup['bootstrap']);
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function scriptEndsOptions() {
      $startup= Runtime::parseArguments(array('-q', 'tools/xar.php'));
      $this->assertEquals(array('-q'), $startup['options']->asArguments());
      $this->assertEquals('tools/xar.php', $startup['bootstrap']);
    }

    /**
     * Test parse() method
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function parseUnknownSwtich() {
      Runtime::parseArguments(array('-@'));
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseMultiSetting() {
      $startup= Runtime::parseArguments(array(
        '-dextension=php_xsl.dll', 
        '-dextension=php_sybase_ct.dll'
      ));
      $this->assertEquals(
        array('php_xsl.dll', 'php_sybase_ct.dll'), 
        $startup['options']->getSetting('extension')
      );
    }

    /**
     * Test parse() method
     *
     */
    #[@test]
    public function parseSwitch() {
      $startup= Runtime::parseArguments(array('-q'));
      $this->assertTrue($startup['options']->getSwitch('q'));
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
      with ($out= $err= '', $p= self::$runtimeExecutable->newInstance($startup->asArguments())); {
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
        $this->runInNewRuntime($this->startupOptions->withSetting('enable_dl', 1), '
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
        $this->runInNewRuntime($this->startupOptions->withSetting('enable_dl', 1), '
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
        $this->runInNewRuntime($this->startupOptions->withSetting('enable_dl', 0), '
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
      echo $this->runInNewRuntime($this->startupOptions->withSetting('enable_dl', 1), '
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
        $this->runInNewRuntime($this->startupOptions, '
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
        $this->runInNewRuntime($this->startupOptions, '
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
      $out= $this->runInNewRuntime($this->startupOptions, '
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
      $out= $this->runInNewRuntime($this->startupOptions, '
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
