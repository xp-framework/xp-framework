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
    'xp.unittest.sources.PackageSource',
    'xp.unittest.sources.EvaluationSource',
    'io.File',
    'unittest.TestSuite',
    'util.Properties',
    'util.collections.Vector',
    'util.cmd.Console'
  );

  /**
   * Unittest command
   * ~~~~~~~~~~~~~~~~
   *
   * Usage:
   * <pre>
   *   unittest [options] test [test [test...]]
   * </pre>
   *
   * Options is one of:
   * <ul>
   *   <li>-v : Be verbose</li>
   *   <li>-cp: Add classpath elements</li>
   * </ul>
   * Tests can one or more of:
   * <ul>
   *   <li>{tests}.ini: A configuration file</li>
   *   <li>{package.name}.*: All classes inside a given package<li>
   *   <li>{Test}.class.php: A class file</li>
   *   <li>{test.class.Name}: A fully qualified class name</li>
   *   <li>-e {test method sourcecode}: Evaluate source</li>
   * </ul>
   *
   * @purpose  Tool
   */
  class xp·unittest·Runner extends Object {

    /**
     * Converts api-doc "markup" to plain text w/ ASCII "art"
     *
     * @param   string markup
     * @return  string text
     */
    protected static function textOf($markup) {
      $line= str_repeat('=', 72);
      return strip_tags(preg_replace(array(
        '#<pre>#', '#</pre>#', '#<li>#',
      ), array(
        $line, $line, '* ',
      ), trim($markup)));
    }

    /**
     * Displays usage and exits
     *
     */
    protected static function usage() {
      Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      exit(1);
    }

    /**
     * Gets an argument
     *
     * @param   string[] args
     * @param   int offset
     * @param   string option
     * @return  string
     * @throws  lang.IllegalArgumentException if no argument exists by this offset
     */
    protected static function arg($args, $offset, $option) {
      if (!isset($args[$offset])) {
        throw new IllegalArgumentException('Option -'.$option.' requires an argument');
      }
      return $args[$offset];
    }

    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (!$args) self::usage();

      // Parse arguments
      $sources= new Vector();
      $verbose= FALSE;
      try {
        for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
          if ('-v' === $args[$i]) {
            $verbose= TRUE;
          } else if ('-cp' === $args[$i]) {
            foreach (explode(PATH_SEPARATOR, self::arg($args, ++$i, 'cp')) as $path) {
              ClassLoader::getDefault()->registerPath($path);
            }
          } else if ('-e' === $args[$i]) {
            $sources->add(new EvaluationSource(self::arg($args, ++$i, 'e')));
          } else if ('-?' === $args[$i]) {
            self::usage();
          } else if (strstr($args[$i], '.ini')) {
            $sources->add(new PropertySource(new Properties($args[$i])));
          } else if (strstr($args[$i], xp::CLASS_FILE_EXT)) {
            $sources->add(new ClassFileSource(new File($args[$i])));
          } else if (strstr($args[$i], '.*')) {
            $sources->add(new PackageSource(Package::forName(substr($args[$i], 0, -2))));
          } else {
            $sources->add(new ClassSource(XPClass::forName($args[$i])));
          }
        }
      } catch (IllegalArgumentException $e) {
        Console::$err->writeLine('*** ', $e->getMessage());
        exit(1);
      }
      
      if ($sources->isEmpty()) {
        Console::$err->writeLine('*** No tests specified');
        exit(1);
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
            $suite->addTestClass($class, $classes[$class]->values);
          } catch (NoSuchElementException $e) {
            Console::writeLine('*** Warning: ', $e->getMessage());
            continue;
          } catch (IllegalArgumentException $e) {
            Console::writeLine('*** Error: ', $e->getMessage());
            return;
          }
        }
      }
      
      // Run it!
      $suite->numTests() && $suite->run();
    }    
  }
?>
