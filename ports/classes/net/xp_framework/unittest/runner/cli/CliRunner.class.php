<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File',
    'unittest.TestSuite',
    'util.Properties',
    'lang.types.ArrayList',
    'util.collections.HashTable',
    'util.cmd.Command'
  );

  /**
   * CLI runner for XP unittests
   *
   * Run all tests contained in a certain property file:
   * <tt> $ unittest unittests.ini </tt>
   *
   * Run a class by its fully qualified name:
   * <tt> $ unittest net.xp_framework.unittest.core.ErrorsTest </tt>
   *
   * Run a class by passing the class' filename:
   * <tt> $ unittest ports/classes/net/xp_framework/unittest/core/IsTest.class.php </tt>
   *
   * Run multiple tests at once:
   * <tt> $ unittest tests.TestOne tests.TestTwo tests.TestThree </tt>
   * <tt> $ unittest tests.TestOne tests/TestTwo.class.php tests/*.ini </tt>
   *
   * @purpose  Runs unittests
   */
  class CliRunner extends Command implements TestListener {
    protected 
      $suite      = NULL,
      $tests      = NULL,
      $arguments  = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->suite= new TestSuite();
      $this->tests= new HashTable();
    }

    /**
     * Load tests from an inifile
     *
     * @param   util.Properties config
     */
    public function loadTestsFromProperties(Properties $config) {
      $this->out->writeLinef(
        '===> Using configuration from %s (%s)', 
        $config->getFilename(),
        $config->readString('this', 'description')
      );

      $section= $config->getFirstSection();
      do {
        if ('this' == $section) continue;   // Ignore special section

        $this->tests->put(
          XPClass::forName($config->readString($section, 'class')),
          new ArrayList($config->readArray($section, 'args'))
        );
      } while ($section= $config->getNextSection());
    }
    
    /**
     * Load tests from class file
     *
     * @param   io.File file
     */
    public function loadTestsFromClassFile(File $file) {
      $this->out->writeLinef('===> Using test file %s', $file->getURI());

      if (!$file->exists()) throw new IllegalArgumentException('File does not exist!');

      $uri= $file->getURI();
      $path= dirname($uri);
      $paths= array_flip(array_filter(array_map('realpath', explode(PATH_SEPARATOR, ini_get('include_path')))));

      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($paths[$path])) {
          $this->tests->put(
            XPClass::forName(strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.')),
            new ArrayList()
          );
          return;
        }

        $path= substr($path, 0, $pos); 
      }
      
      throw new IllegalArgumentException('Cannot load class from '.$file->toString());
    }

    /**
     * Load tests from class file
     *
     * @param   io.File class
     */
    public function loadTestsFromClass(XPClass $class) {
      $this->out->writeLinef('===> Using test class %s', $class->toString());
      $this->tests->put($class, new ArrayList());
    }

    /**
     * Set runner targets. This is one of the following:
     *
     * - A property file (recognized via .ini-extension)
     * - A class file (recognized via .class.php-extension)
     * - A fully qualified class name
     *
     * @param   string[] targets
     */
    #[@args]
    public function setTargets($targets) {
      for ($i= 0, $s= sizeof($targets); $i < $s; $i++) {
        if ('-' === $targets[$i]{0}) {
          $i+= '-' === $targets[$i]{1} ? 0 : 1;    // Ignore
        } else if (strstr($targets[$i], '.ini')) {
          $current= $this->loadTestsFromProperties(new Properties($targets[$i]));
        } else if (strstr($targets[$i], '.class.php')) {
          $this->loadTestsFromClassFile(new File($targets[$i]));
        } else {
          $this->loadTestsFromClass(XPClass::forName($targets[$i]));
        }
      }
    }
    
    /**
     * Set arguments that should be passed to the tests' constructors.
     *
     * @param   string arguments default ''
     */
    #[@arg]
    public function setArguments($arguments= '') {
      $this->arguments= ('' == trim($arguments) 
        ? array() 
        : array_map('trim', explode(',', $arguments))
      );
      $this->out->writeLinef(
        '---> Have arguments [%s]',
        implode(', ', array_map(array('xp', 'stringOf'), $this->arguments))
      );
    }
    
    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {
      // NOOP
    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {
      $this->out->write('F');
    }
    
    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {
      $this->out->write('.');
    }
    
    /**
     * Called when a test is not run - usually because it is skipped
     * due to a non-met prerequisite or if it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {
      $this->out->write('S');
    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      $this->out->writeLine('===> Running test suite');
    }
    
    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $this->out->writeLine();
      $this->out->writeLine('===> ', $result);
    }

    /**
     * Runs the test suite
     *
     */
    public function run() {
      $this->out->writeLine('===> Setting up suite');
      
      foreach ($this->tests->keys() as $class) {
        $arguments= $this->tests->get($class);

        try {
          $this->suite->addTestClass(
            $class,
            $arguments->length ? $arguments->values : $this->arguments
          );
        } catch (NoSuchElementException $e) {
          $this->out->writeLine('*** Warning: ', $e->getMessage());
          return;
        } catch (IllegalArgumentException $e) {
          $this->out->writeLine('*** Error: ', $e->getMessage());
          return;
        }
      }
      
      $this->suite->addListener($this);
      $this->suite->run();
    }
  }
?>
