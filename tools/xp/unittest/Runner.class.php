<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.unittest';

  uses(
    'xp.unittest.DefaultListener',
    'xp.unittest.VerboseListener',
    'xp.unittest.sources.PropertySource',
    'xp.unittest.sources.ClassSource',
    'xp.unittest.sources.ClassFileSource',
    'io.File',
    'unittest.TestSuite',
    'util.Properties',
    'util.collections.Vector',
    'util.cmd.Console'
  );

  /**
   * Unittest command
   *
   * @purpose  Tool
   */
  class xp·unittest·Runner extends Object {

    /**
     * Displays usage and exists
     *
     */
    protected static function usage() {
      Console::$err->writeLine('*** Usage: unittest [-v] Test.class.php [tests.ini [test.class.Name]]');
      exit(1);
    }

    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (!$args) self::usage();

      $sources= new Vector();

      // Parse arguments
      $verbose= FALSE;
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('-v' === $args[$i]) {
          $verbose= TRUE;
        } else if ('-cp' === $args[$i]) {
          foreach (explode(PATH_SEPARATOR, $args[++$i]) as $path) {
            ClassLoader::getDefault()->registerPath($path);
          }
        } else if (strstr($args[$i], '.ini')) {
          $sources->add(new PropertySource(new Properties($args[$i])));
        } else if (strstr($args[$i], '.class.php')) {
          $sources->add(new ClassFileSource(new File($args[$i])));
        } else {
          $sources->add(new ClassSource(XPClass::forName($args[$i])));
        }
      }
      
      // Setup suite
      $suite= new TestSuite();
      $suite->addListener($verbose 
        ? new VerboseListener(Console::$out)
        : new DefaultListener(Console::$out)
      );
      
      // Add test classes
      foreach ($sources as $source) {
        $verbose && Console::writeLine('===> Adding test classes from ', $source);
        $classes= $source->testClasses();
        foreach ($classes->keys() as $class) {
          try {
            $suite->addTestClass($class);
          } catch (NoSuchElementException $e) {
            Console::writeLine('*** Warning: ', $e->getMessage());
            return;
          } catch (IllegalArgumentException $e) {
            Console::writeLine('*** Error: ', $e->getMessage());
            return;
          }
        }
      }

      // Run it!
      $suite->run();
    }    
  }
?>
