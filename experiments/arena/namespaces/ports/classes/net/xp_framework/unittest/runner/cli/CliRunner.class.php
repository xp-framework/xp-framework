<?php
/* This class is part of the XP framework
 *
 * $Id: CliRunner.class.php 10936 2007-08-24 09:57:57Z gelli $ 
 */

  namespace net::xp_framework::unittest::runner::cli;

  ::uses(
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
   * @purpose  Runs unittests
   */
  class CliRunner extends util::cmd::Command {
    protected 
      $suite      = NULL,
      $tests      = NULL,
      $arguments  = array();

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->suite= new unittest::TestSuite();
      $this->tests= new util::collections::HashTable();
    }

    /**
     * Load tests from an inifile
     *
     * @param   util.Properties config
     */
    public function loadTestsFromProperties( $config) {
      $this->out->writeLinef(
        '===> Using configuration from %s (%s)', 
        $config->getFilename(),
        $config->readString('this', 'description')
      );

      $section= $config->getFirstSection();
      do {
        if ('this' == $section) continue;   // Ignore special section

        $this->tests->put(
          lang::XPClass::forName($config->readString($section, 'class')),
          new lang::types::ArrayList($config->readArray($section, 'args'))
        );
      } while ($section= $config->getNextSection());
    }
    
    /**
     * Load tests from class file
     *
     * @param   io.File file
     */
    public function loadTestsFromClassFile( $file) {
      $this->out->writeLinef('===> Using test file %s', $file->getURI());

      if (!$file->exists()) throw new lang::IllegalArgumentException('File does not exist!');

      $uri= $file->getURI();
      $path= dirname($uri);
      $paths= array_flip(array_map('realpath', explode(PATH_SEPARATOR, ini_get('include_path'))));

      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($paths[$path])) {
          $this->tests->put(
            lang::XPClass::forName(strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.')),
            new lang::types::ArrayList()
          );
          return;
        }

        $path= substr($path, 0, $pos); 
      }
      
      throw new lang::IllegalArgumentException('Cannot load class from '.$file->toString());
    }

    /**
     * Load tests from class file
     *
     * @param   io.File class
     */
    public function loadTestsFromClass( $class) {
      $this->out->writeLinef('===> Using test class %s', $class->toString());
      $this->tests->put($class, new lang::types::ArrayList());
    }

    /**
     * Set runner target. This is one of the following:
     *
     * - A property file (recognized via .ini-extension)
     * - A class file (recognized via .class.php-extension)
     * - A fully qualified class name
     *
     * @param   string target
     */
    #[@arg(position= 0)]
    public function setTarget($target) {
      if (strstr($target, '.ini')) {
        $this->loadTestsFromProperties(new util::Properties($target));
      } else if (strstr($target, '.class.php')) {
        $this->loadTestsFromClassFile(new io::File($target));
      } else {
        $this->loadTestsFromClass(lang::XPClass::forName($target));
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
     * Runs the test suite
     *
     */
    public function run() {
      $this->out->writeLine('===> Setting up suite');
      
      foreach ($this->tests->keys() as $class) {
        $arguments= $this->tests->get($class);

        try {
          $ignored= $this->suite->addTestClass(
            $class, 
            $arguments->values[0] ? $arguments->values[0] : $this->arguments
          );
        } catch (util::NoSuchElementException $e) {
          $this->out->writeLine('*** Warning: ', $e->getMessage());
          return;
        } catch (lang::IllegalArgumentException $e) {
          $this->out->writeLine('*** Error: ', $e->getMessage());
          return;
        }

        foreach ($ignored as $method) {
          $this->out->writeLinef(
            '     >> Ignoring %s::%s (%s)', 
            $class->getName(TRUE), 
            $method->getName(),
            $method->getAnnotation('ignore')
          );
        }
      }
      
      $this->out->writeLine('===> Running test suite');
      $this->out->writeLine($this->suite->run());
    }
  }
?>
