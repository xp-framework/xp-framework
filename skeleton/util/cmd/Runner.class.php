<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.StringWriter', 
    'io.streams.ConsoleOutputStream',
    'util.log.Logger',
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );

  /**
   * Runs util.cmd.Command subclasses on the command line.
   *
   * Usage:
   * <pre>
   * $ xpcli [options] fully.qualified.class.Name [classoptions]
   * </pre>
   *
   * Options includes one of the following:
   * <pre>
   * --config | -c:
   *   Set the path with which the PropertyManager is configured with. The
   *   PropertyManager is used for dependency injection. If a file called
   *   log.ini exists in this path, the Logger will be configured with. If
   *   a database.ini is present there, the ConnectionManager will be #
   *   configured with it.
   * 
   * </pre>
   *
   * @test     xp://net.xp_framework.unittest.util.cmd.RunnerTest
   * @see      xp://util.cmd.Command
   * @purpose  Runner
   */
  class Runner extends Object {
    private static
      $out    = NULL,
      $err    = NULL;
    
    static function __static() {
      self::$out= new StringWriter(new ConsoleOutputStream(STDOUT));
      self::$err= new StringWriter(new ConsoleOutputStream(STDERR));
    }
  
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
     * Show usage
     *
     * @param   lang.XPClass class
     */
    public static function showUsage(XPClass $class) {

      // Description
      if (NULL !== ($comment= $class->getComment())) {
        self::$err->writeLine(self::textOf($comment));
        self::$err->writeLine(str_repeat('=', 72));
      }

      $extra= $details= $positional= array();
      foreach ($class->getMethods() as $method) {
        if (!$method->hasAnnotation('arg')) continue;

        $arg= $method->getAnnotation('arg');
        $name= strtolower(preg_replace('/^set/', '', $method->getName()));;
        $comment= self::textOf($method->getComment());

        if (0 == $method->numArguments()) {
          $optional= TRUE;
        } else {
          list($first, )= $method->getArguments();
          $optional= $first->isOptional();
        }

        if (isset($arg['position'])) {
          $details['#'.($arg['position'] + 1)]= $comment;
          $positional[$arg['position']]= $name;
        } else if (isset($arg['name'])) {
          $details['--'.$arg['name'].' | -'.(isset($arg['short']) ? $arg['short'] : $arg['name']{0})]= $comment;
          $extra[$arg['name']]= $optional;
        } else {
          $details['--'.$name.' | -'.(isset($arg['short']) ? $arg['short'] : $name{0})]= $comment;
          $extra[$name]= $optional;
        }
      }

      // Usage
      asort($positional);
      self::$err->write('Usage: $ xpcli ', $class->getName(), ' ');
      foreach ($positional as $name) {
        self::$err->write('<', $name, '> ');
      }
      foreach ($extra as $name => $optional) {
        self::$err->write(($optional ? '[' : ''), '--', $name, ($optional ? '] ' : ' '));
      }
      self::$err->writeLine();

      // Argument details
      self::$err->writeLine('Arguments:');
      foreach ($details as $which => $comment) {
        self::$err->writeLine('* ', $which, "\n  ", $comment, "\n");
      }
    }
  
    /**
     * Main method
     *
     * @param   string[] args
     * @return  int
     */
    public static function main(array $args) {
      return create(new self())->run(new ParamString($args));
    }
    
    /**
     * Reassigns standard output stream
     *
     * @param   io.streams.OutputStream out
     * @return  io.streams.OutputStream the given output stream
     */
    public function setOut(OutputStream $out) {
      self::$out= new StringWriter($out);
      return $out;
    }

    /**
     * Reassigns standard error stream
     *
     * @param   io.streams.OutputStream error
     * @return  io.streams.OutputStream the given output stream
     */
    public function setErr(OutputStream $err) {
      self::$err= new StringWriter($err);
      return $err;
    }
    
    /**
     * Main method
     *
     * @param   util.cmd.ParamString params
     * @return  int
     */
    public function run(ParamString $params) {

      // No arguments given - show our own usage
      if ($params->count <= 1) {
        self::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
        return 1;
      }

      // Separate runner options from class options
      $map= array();
      $options= array(
        'config'  => 'etc'
      );
      $valid= array(
        'config'  => 1,
      );
      foreach ($valid as $key => $val) {
        $valid[$key{0}]= $val;
        $map[$key{0}]= $key;
      }
      $classname= NULL;
      for ($i= 1; $i < $params->count; $i++) {
        $option= $params->list[$i];

        if (0 == strncmp($option, '--', 2)) {        // Long: --foo / --foo=bar
          $p= strpos($option, '=');
          $name= substr($option, 2, FALSE === $p ? strlen($option) : $p- 2);
          if (isset($valid[$name])) {
            if ($valid[$name] == 1) {
              $options[$name]= FALSE === $p ? NULL : substr($option, $p+ 1);
            } else {
              $options[$name]= TRUE;
            }
          }
        } else if (0 == strncmp($option, '-', 1)) {   // Short: -f / -f bar
          $short= substr($option, 1);
          if (isset($valid[$short])) {
            if ($valid[$short] == 1) {
              $options[$map[$short]]= $params->list[++$i];
            } else {
              $options[$map[$short]]= TRUE;
            }
          }
        } else {
          unset($params->list[-1]);
          $classname= $option;
          $classparams= new ParamString(array_slice($params->list, $i+ 1));
          break;
        }
      }

      // Sanity check
      if (!$classname) {
        self::$err->writeLine('*** Missing classname');
        return 1;
      } else if (strstr($classname, xp::CLASS_FILE_EXT)) {
        $file= new File($classname);
        if (!$file->exists()) {
          self::$err->writeLine('*** Cannot load class from non-existant file ', $classname);
          return 1;
        }
        $uri= $file->getURI();
        $path= dirname($uri);
        $paths= array_flip(array_map('realpath', xp::$registry['classpath']));
        $class= NULL;
        while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
          if (isset($paths[$path])) {
            $class= XPClass::forName(strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.'));
            break;
          }

          $path= substr($path, 0, $pos); 
        }

        if (!$class) {
          self::$err->writeLine('*** Cannot load class from ', $file);
          return 1;
        }
      } else {
        try {
          $class= XPClass::forName($classname);
        } catch (ClassNotFoundException $e) {
          self::$err->writeLine('*** ', $e->getMessage());
          return 1;
        }
      }
      
      // Check whether class is runnable
      if (!$class->isSubclassOf('lang.Runnable')) {
          self::$err->writeLine('*** ', $class->getName(), ' is not runnable');
          return 1;
      }

      // Usage
      if ($classparams->exists('help', '?')) {
        self::showUsage($class);
        return 0;
      }

      // Load, instantiate and initialize
      $pm= PropertyManager::getInstance();
      $pm->configure($options['config']);

      $l= Logger::getInstance();
      $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));

      $cm= ConnectionManager::getInstance();
      $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));

      $instance= $class->newInstance();
      $instance->out= self::$out;
      $instance->err= self::$err;

      foreach ($class->getMethods() as $method) {
        if ($method->hasAnnotation('inject')) {      // Perform injection
          $inject= $method->getAnnotation('inject');
          switch ($inject['type']) {
            case 'rdbms.DBConnection': {
              $args= array($cm->getByHost($inject['name'], 0));
              break;
            }

            case 'util.Properties': {
              $args= array($pm->getProperties($inject['name']));
              break;
            }

            case 'util.log.LogCategory': {
              $args= array($l->getCategory($inject['name']));
              break;
            }

            default: {
              self::$err->writeLine('*** Unknown injection type "'.$inject['type'].'"');
              return 2;
            }
          }

          try {
            $method->invoke($instance, $args);
          } catch (Throwable $e) {
            self::$err->writeLine('*** Error injecting '.$inject['name'].': '.$e->getMessage());
            return 2;
          }
        } else if ($method->hasAnnotation('args')) { // Pass all arguments
          $pass= array();
          foreach (preg_split('/, ?/', $method->getAnnotation('args', 'select')) as $def) {
            if (is_numeric($def) || '-' == $def{0}) {
              $pass[]= $classparams->value((int)$def);
            } else {
              sscanf($def, '[%d..%d]', $begin, $end);
              isset($begin) || $begin= 0;
              isset($end) || $end= $classparams->count- 1;
            
              while ($begin <= $end) {
                $pass[]= $classparams->value($begin++);
              }
            }
          }
          try {
            $method->invoke($instance, array($pass));
          } catch (Throwable $e) {
            self::$err->writeLine('*** Error for arguments '.$begin.'..'.$end.': '.$e->getMessage());
            return 2;
          }
        } else if ($method->hasAnnotation('arg')) {  // Pass arguments
          $arg= $method->getAnnotation('arg');
          if (isset($arg['position'])) {
            $name= '#'.($arg['position']+ 1);
            $select= intval($arg['position']);
            $short= NULL;
          } else if (isset($arg['name'])) {
            $name= $select= $arg['name'];
            $short= isset($arg['short']) ? $arg['short'] : NULL;
          } else {
            $name= $select= strtolower(preg_replace('/^set/', '', $method->getName()));
            $short= isset($arg['short']) ? $arg['short'] : NULL;
          }

          if (0 == $method->numArguments()) {
            if (!$classparams->exists($select, $short)) continue;
            $args= array();
          } else if (!$classparams->exists($select, $short)) {
            list($first, )= $method->getArguments();
            if (!$first->isOptional()) {
              self::$err->writeLine('*** Argument '.$name.' does not exist!');
              return 2;
            }

            $args= array();
          } else {
            $args= array($classparams->value($select, $short));
          }

          try {
            $method->invoke($instance, $args);
          } catch (Throwable $e) {
            self::$err->writeLine('*** Error for argument '.$name.': '.$e->getMessage());
            return 2;
          }
        }
      }

      $instance->run();
      return 0;
    }
  }
?>
