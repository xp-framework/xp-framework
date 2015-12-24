<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.unittest';

  uses(
    'xp.unittest.TestListeners',
    'xp.unittest.sources.PropertySource',
    'xp.unittest.sources.ClassSource',
    'xp.unittest.sources.ClassFileSource',
    'xp.unittest.sources.PackageSource',
    'xp.unittest.sources.EvaluationSource',
    'xp.unittest.sources.FolderSource',
    'io.streams.FileOutputStream',
    'io.File',
    'io.Folder',
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
   *   <li>-q : Be quiet (no output)</li>
   *   <li>-cp: Add classpath elements</li>
   *   <li>-a {argument}: Define argument to pass to tests (may be used
   *     multiple times)</li>
   *   <li>-l {listener.class.Name} {output} [-o option value [-o ...]]
   *     where output is either "-" for console output or a file name. 
   *     Options with "-o" are listener-dependant arguments.</li>
   *   <li>--color={mode} : Enable / disable color; mode can be one of
   *     . "on" - activate color mode
   *     . "off" - disable color mode
   *     . "auto" - try to determine whether colors are supported and enable
   *       accordingly.
   *   </li>
   * </ul>
   * Tests can be one or more of:
   * <ul>
   *   <li>{tests}.ini: A configuration file</li>
   *   <li>{package.name}.*: All classes inside a given package</li>
   *   <li>{package.name}.**: All classes inside a given package and all subpackages</li>
   *   <li>{Test}.class.php: A class file</li>
   *   <li>{test.class.Name}: A fully qualified class name</li>
   *   <li>{test.class.Name}::{testName}: A fully qualified class name and a test name</li>
   *   <li>path/with/tests: All classes inside a given directory and all subdirectories</li>
   *   <li>-e {test method sourcecode}: Evaluate source</li>
   * </ul>
   *
   * @test     xp://net.xp_framework.unittest.tests.UnittestRunnerTest
   * @purpose  Tool
   */
  class xp暉nittest愛unner extends Object {
    protected $out= NULL;
    protected $err= NULL;
    
    /**
     * Constructor. Initializes out and err members to console
     *
     */
    public function __construct() {
      $this->out= Console::$out;
      $this->err= Console::$err;
    }

    /**
     * Reassigns standard output stream
     *
     * @param   io.streams.OutputStream out
     * @return  io.streams.OutputStream the given output stream
     */
    public function setOut(OutputStream $out) {
      $this->out= new StringWriter($out);
      return $out;
    }

    /**
     * Reassigns standard error stream
     *
     * @param   io.streams.OutputStream error
     * @return  io.streams.OutputStream the given output stream
     */
    public function setErr(OutputStream $err) {
      $this->err= new StringWriter($err);
      return $err;
    }

    /**
     * Converts api-doc "markup" to plain text w/ ASCII "art"
     *
     * @param   string markup
     * @return  string text
     */
    protected function textOf($markup) {
      $line= str_repeat('=', 72);
      return strip_tags(preg_replace(array(
        '#<pre>#', '#</pre>#', '#<li>#',
      ), array(
        $line, $line, '* ',
      ), trim($markup)));
    }

    /**
     * Displays usage
     *
     * @return  int exitcode
     */
    protected function usage() {
      $this->err->writeLine($this->textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      return 1;
    }

    /**
     * Displays listener usage
     *
     * @return  int exitcode
     */
    protected function listenerUsage($listener) {
      $this->err->writeLine($this->textOf($listener->getComment()));
      $positional= $options= array();
      foreach ($listener->getMethods() as $method) {
        if ($method->hasAnnotation('arg')) {
          $arg= $method->getAnnotation('arg');
          $name= strtolower(preg_replace('/^set/', '', $method->getName()));
          if (isset($arg['position'])) {
            $positional[$arg['position']]= $name;
            $options['<'.$name.'>']= $method;
          } else {
            $name= isset($arg['name']) ? $arg['name'] : $name;
            $short= isset($arg['short']) ? $arg['short'] : $name{0};
            $param= ($method->numParameters() > 0 ? ' <'.$method->getParameter(0)->getName().'>' : '');
            $options[$name.'|'.$short.$param]= $method;
          }
        }
      }
      $this->err->writeLine();
      $this->err->write('Usage: -l ', $listener->getName(), ' ');
      ksort($positional);
      foreach ($positional as $name) {
        $this->err->write('-o <', $name, '> ');
      }
      if ($options) {
        $this->err->writeLine('[options]');
        $this->err->writeLine();
        foreach ($options as $name => $method) {
          $this->err->writeLine('  * -o ', $name, ': ', self::textOf($method->getComment()));
        }
      } else {
        $this->err->writeLine();
      }
      return 1;
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
    protected function arg($args, $offset, $option) {
      if (!isset($args[$offset])) {
        throw new IllegalArgumentException('Option -'.$option.' requires an argument');
      }
      return $args[$offset];
    }
    
    /**
     * Returns an output stream writer for a given file name.
     *
     * @param   string in
     * @return  io.streams.OutputStreamWriter
     */
    protected function streamWriter($in) {
      if ('-' == $in) {
        return Console::$out;
      } else {
        return new StringWriter(new FileOutputStream($in));
      }
    }
    
    /**
     * Runs suite
     *
     * @param   string[] args
     * @return  int exitcode
     */
    public function run(array $args) {
      if (!$args) return $this->usage();

      // Setup suite
      $suite= new TestSuite();

      // Parse arguments
      $sources= new Vector();
      $listener= TestListeners::$DEFAULT;
      $arguments= array();
      $colors= NULL;
      $cmap= array(
        ''      => NULL,
        '=on'   => TRUE,
        '=off'  => FALSE,
        '=auto' => NULL
      );

      try {
        for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
          if ('-v' == $args[$i]) {
            $listener= TestListeners::$VERBOSE;
          } else if ('-q' == $args[$i]) {
            $listener= TestListeners::$QUIET;
          } else if ('-cp' == $args[$i]) {
            foreach (explode(PATH_SEPARATOR, $this->arg($args, ++$i, 'cp')) as $element) {
              ClassLoader::registerPath($element, NULL);
            }
          } else if ('-e' == $args[$i]) {
            $sources->add(new xp暉nittest新ources幌valuationSource($this->arg($args, ++$i, 'e')));
          } else if ('-l' == $args[$i]) {
            $arg= $this->arg($args, ++$i, 'l');
            $class= XPClass::forName(strstr($arg, '.') ? $arg : 'xp.unittest.'.ucfirst($arg).'Listener');
            $arg= $this->arg($args, ++$i, 'l');
            if ('-?' == $arg || '--help' == $arg) {
              return $this->listenerUsage($class);
            }
            $output= $this->streamWriter($arg);
            $instance= $suite->addListener($class->newInstance($output));

            // Get all @arg-annotated methods
            $options= array();
            foreach ($class->getMethods() as $method) {
              if ($method->hasAnnotation('arg')) {
                $arg= $method->getAnnotation('arg');
                if (isset($arg['position'])) {
                  $options[$arg['position']]= $method;
                } else {
                  $name= isset($arg['name']) ? $arg['name'] : strtolower(preg_replace('/^set/', '', $method->getName()));
                  $short= isset($arg['short']) ? $arg['short'] : $name{0};
                  $options[$name]= $options[$short]= $method;
                }
              }
            }
            $option= 0;
          } else if ('-o' == $args[$i]) {
            if (isset($options[$option])) {
              $name= '#'.($option+ 1);
              $method= $options[$option];
            } else {
              $name= $this->arg($args, ++$i, 'o');
              if (!isset($options[$name])) {
                $this->err->writeLine('*** Unknown listener argument '.$name.' to '.$instance->getClassName());
                return 2;
              }
              $method= $options[$name];
            }
            $option++;
            if (0 == $method->numParameters()) {
              $pass= array();
            } else {
              $pass= $this->arg($args, ++$i, 'o '.$name);
            }
            try {
              $method->invoke($instance, $pass);
            } catch (TargetInvocationException $e) {
              $this->err->writeLine('*** Error for argument '.$name.' to '.$instance->getClassName().': '.$e->getCause()->toString());
              return 2;
            }
          } else if ('-?' == $args[$i] || '--help' == $args[$i]) {
            return $this->usage();
          } else if ('-a' == $args[$i]) {
            $arguments[]= $this->arg($args, ++$i, 'a');
          } else if ('--color' == substr($args[$i], 0, 7)) {
            $remainder= (string)substr($args[$i], 7);
            if (!array_key_exists($remainder, $cmap)) {
              throw new IllegalArgumentException('Unsupported argument for --color (must be <empty>, "on", "off", "auto" (default))');
            }
            $colors= $cmap[$remainder];
          } else if (strstr($args[$i], '.ini')) {
            $sources->add(new xp暉nittest新ources感ropertySource(new Properties($args[$i])));
          } else if (strstr($args[$i], xp::CLASS_FILE_EXT)) {
            $sources->add(new xp暉nittest新ources嵩lassFileSource(new File($args[$i])));
          } else if (strstr($args[$i], '.**')) {
            $sources->add(new xp暉nittest新ources感ackageSource(Package::forName(substr($args[$i], 0, -3)), TRUE));
          } else if (strstr($args[$i], '.*')) {
            $sources->add(new xp暉nittest新ources感ackageSource(Package::forName(substr($args[$i], 0, -2))));
          } else if (FALSE !== ($p= strpos($args[$i], '::'))) {
            $sources->add(new xp暉nittest新ources嵩lassSource(XPClass::forName(substr($args[$i], 0, $p)), substr($args[$i], $p+ 2)));
          } else if (is_dir($args[$i])) {
            $sources->add(new xp暉nittest新ources幹olderSource(new Folder($args[$i])));
          } else {
            $sources->add(new xp暉nittest新ources嵩lassSource(XPClass::forName($args[$i])));
          }
        }
      } catch (Throwable $e) {
        $this->err->writeLine('*** ', $e->getMessage());
        xp::gc();
        return 1;
      }
      
      if ($sources->isEmpty()) {
        $this->err->writeLine('*** No tests specified');
        return 1;
      }
      
      // Set up suite
      $l= $suite->addListener($listener->newInstance($this->out));
      if ($l instanceof ColorizingListener) {
        $l->setColor($colors);
      }

      foreach ($sources as $source) {
        try {
          $tests= $source->testCasesWith($arguments);
          foreach ($tests as $test) {
            $suite->addTest($test);
          }
        } catch (NoSuchElementException $e) {
          $this->err->writeLine('*** Warning: ', $e->getMessage());
          continue;
        } catch (IllegalArgumentException $e) {
          $this->err->writeLine('*** Error: ', $e->getMessage());
          return 1;
        } catch (MethodNotImplementedException $e) {
          $this->err->writeLine('*** Error: ', $e->getMessage(), ': ', $e->method, '()');
          return 1;
        }
      }
      
      // Run it!
      if (0 == $suite->numTests()) {
        return 3;
      } else {
        $r= $suite->run();
        return $r->failureCount() > 0 ? 1 : 0;
      }
    }

    /**
     * Main runner method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      return create(new self())->run($args);
    }    
  }
?>
