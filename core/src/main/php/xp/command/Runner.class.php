<?php namespace xp\command;

use util\cmd\ParamString;
use io\streams\StringReader;
use io\streams\StringWriter;
use io\streams\ConsoleInputStream;
use io\streams\ConsoleOutputStream;
use util\log\Logger;
use util\log\context\EnvironmentAware;
use util\PropertyManager;
use util\FilesystemPropertySource;
use util\ResourcePropertySource;
use rdbms\ConnectionManager;

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
 * -c:
 *   Add the path to the PropertyManager sources. The PropertyManager
 *   is used for dependency injection. If files called log.ini exists
 *   in this paths, the Logger will be configured with. If any
 *   database.ini are present there, the ConnectionManager will be
 *   configured with it. (If not given etc is used as default path)
 * 
 * -cp:
 *   Add the path value to the class path.
 *
 * -v:
 *   Enable verbosity (show complete stack trace when exceptions
 *   occurred)
 * </pre>
 *
 * @test     xp://net.xp_framework.unittest.util.cmd.RunnerTest
 * @see      xp://util.cmd.Command
 * @purpose  Runner
 */
class Runner extends \lang\Object {
  private static
    $in     = null,
    $out    = null,
    $err    = null;
  
  private
    $verbose= false;

  const DEFAULT_CONFIG_PATH = 'etc';
  
  static function __static() {
    self::$in= new StringReader(new ConsoleInputStream(STDIN));
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
  public static function showUsage(\lang\XPClass $class) {

    // Description
    if (null !== ($comment= $class->getComment())) {
      self::$err->writeLine(self::textOf($comment));
      self::$err->writeLine(str_repeat('=', 72));
    }

    $extra= $details= $positional= array();
    foreach ($class->getMethods() as $method) {
      if (!$method->hasAnnotation('arg')) continue;

      $arg= $method->getAnnotation('arg');
      $name= strtolower(preg_replace('/^set/', '', $method->getName()));;
      $comment= self::textOf($method->getComment());

      if (0 == $method->numParameters()) {
        $optional= true;
      } else {
        list($first, )= $method->getParameters();
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
      self::$err->writeLine('* ', $which, "\n  ", str_replace("\n", "\n  ", $comment), "\n");
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
   * Reassigns standard input stream
   *
   * @param   io.streams.InputStream in
   * @return  io.streams.InputStream the given output stream
   */
  public function setIn(\io\streams\InputStream $in) {
    self::$in= new StringReader($in);
    return $in;
  }
  
  /**
   * Reassigns standard output stream
   *
   * @param   io.streams.OutputStream out
   * @return  io.streams.OutputStream the given output stream
   */
  public function setOut(\io\streams\OutputStream $out) {
    self::$out= new StringWriter($out);
    return $out;
  }

  /**
   * Reassigns standard error stream
   *
   * @param   io.streams.OutputStream error
   * @return  io.streams.OutputStream the given output stream
   */
  public function setErr(\io\streams\OutputStream $err) {
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
    if ($params->count < 1) {
      self::$err->writeLine(self::textOf(\lang\XPClass::forName(\xp::nameOf(__CLASS__))->getComment()));
      return 1;
    }

    // Configure properties
    $pm= PropertyManager::getInstance();

    // Separate runner options from class options
    for ($offset= 0, $i= 0; $i < $params->count; $i++) switch ($params->list[$i]) {
      case '-c':
        if (0 == strncmp('res://', $params->list[$i+ 1], 6)) {
          $pm->appendSource(new ResourcePropertySource(substr($params->list[$i+ 1], 6)));
        } else {
          $pm->appendSource(new FilesystemPropertySource($params->list[$i+ 1]));
        }
        $offset+= 2; $i++;
        break;
      case '-cp':
        \lang\ClassLoader::registerPath($params->list[$i+ 1], null);
        $offset+= 2; $i++;
        break;
      case '-v':
        $this->verbose= true;
        $offset+= 1; $i++;
        break;
      default:
        break 2;
    }
    
    // Sanity check
    if (!$params->exists($offset)) {
      self::$err->writeLine('*** Missing classname');
      return 1;
    }
    
    // Use default path for PropertyManager if no sources set
    if (!$pm->getSources()) {
      $pm->configure(self::DEFAULT_CONFIG_PATH);
    }

    unset($params->list[-1]);
    $classname= $params->value($offset);
    $classparams= new ParamString(array_slice($params->list, $offset+ 1));

    // Class file or class name
    if (strstr($classname, \xp::CLASS_FILE_EXT)) {
      $file= new \io\File($classname);
      if (!$file->exists()) {
        self::$err->writeLine('*** Cannot load class from non-existant file ', $classname);
        return 1;
      }

      try {
        $class= \lang\ClassLoader::getDefault()->loadUri($file->getURI());
      } catch (\lang\ClassNotFoundException $e) {
        self::$err->writeLine('*** ', $this->verbose ? $e : $e->getMessage());
        return 1;
      }
    } else {
      try {
        $class= \lang\XPClass::forName($classname);
      } catch (\lang\ClassNotFoundException $e) {
        self::$err->writeLine('*** ', $this->verbose ? $e : $e->getMessage());
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
    $l= Logger::getInstance();
    $pm->hasProperties('log') && $l->configure($pm->getProperties('log'));

    $cm= ConnectionManager::getInstance();
    $pm->hasProperties('database') && $cm->configure($pm->getProperties('database'));

    // Setup logger context for all registered log categories
    foreach (Logger::getInstance()->getCategories() as $category) {
      if (null === ($context= $category->getContext()) || !($context instanceof EnvironmentAware)) continue;
      $context->setHostname(\lang\System::getProperty('host.name'));
      $context->setRunner($this->getClassName());
      $context->setInstance($class->getName());
      $context->setResource(null);
      $context->setParams($params->string);
    }

    $instance= $class->newInstance();
    $instance->in= self::$in;
    $instance->out= self::$out;
    $instance->err= self::$err;
    $methods= $class->getMethods();

    // Injection
    foreach ($methods as $method) {
      if (!$method->hasAnnotation('inject')) continue;

      $inject= $method->getAnnotation('inject');
      if (isset($inject['type'])) {
        $type= $inject['type'];
      } else if ($restriction= $method->getParameter(0)->getTypeRestriction()) {
        $type= $restriction->getName();
      } else {
        $type= $method->getParameter(0)->getType()->getName();
      }
      try {
        switch ($type) {
          case 'rdbms.DBConnection': {
            $args= array($cm->getByHost($inject['name'], 0));
            break;
          }

          case 'util.Properties': {
            $p= $pm->getProperties($inject['name']);

            // If a PropertyAccess is retrieved which is not a util.Properties,
            // then, for BC sake, convert it into a util.Properties
            if (
              $p instanceof \util\PropertyAccess &&
              !$p instanceof \util\Properties
            ) {
              $convert= \util\Properties::fromString('');

              $section= $p->getFirstSection();
              while ($section) {
                // HACK: Properties::writeSection() would first attempts to
                // read the whole file, we cannot make use of it.
                $convert->_data[$section]= $p->readSection($section);
                $section= $p->getNextSection();
              }

              $args= array($convert);
            } else {
              $args= array($p);
            }
            break;
          }

          case 'util.log.LogCategory': {
            $args= array($l->getCategory($inject['name']));
            break;
          }

          default: {
            self::$err->writeLine('*** Unknown injection type "'.$type.'" at method "'.$method->getName().'"');
            return 2;
          }
        }

        $method->invoke($instance, $args);
      } catch (\lang\reflect\TargetInvocationException $e) {
        self::$err->writeLine('*** Error injecting '.$type.' '.$inject['name'].': '.$e->getCause()->compoundMessage());
        return 2;
      } catch (\lang\Throwable $e) {
        self::$err->writeLine('*** Error injecting '.$type.' '.$inject['name'].': '.$e->compoundMessage());
        return 2;
      }
    }
    
    // Arguments
    foreach ($methods as $method) {
      if ($method->hasAnnotation('args')) { // Pass all arguments
        if (!$method->hasAnnotation('args', 'select')) {
          $begin= 0;
          $end= $classparams->count;
          $pass= array_slice($classparams->list, 0, $end);
        } else {
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
        }
        try {
          $method->invoke($instance, array($pass));
        } catch (\lang\Throwable $e) {
          self::$err->writeLine('*** Error for arguments '.$begin.'..'.$end.': ', $this->verbose ? $e : $e->getMessage());
          return 2;
        }
      } else if ($method->hasAnnotation('arg')) {  // Pass arguments
        $arg= $method->getAnnotation('arg');
        if (isset($arg['position'])) {
          $name= '#'.($arg['position']+ 1);
          $select= intval($arg['position']);
          $short= null;
        } else if (isset($arg['name'])) {
          $name= $select= $arg['name'];
          $short= isset($arg['short']) ? $arg['short'] : null;
        } else {
          $name= $select= strtolower(preg_replace('/^set/', '', $method->getName()));
          $short= isset($arg['short']) ? $arg['short'] : null;
        }

        if (0 == $method->numParameters()) {
          if (!$classparams->exists($select, $short)) continue;
          $args= array();
        } else if (!$classparams->exists($select, $short)) {
          list($first, )= $method->getParameters();
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
        } catch (\lang\reflect\TargetInvocationException $e) {
          self::$err->writeLine('*** Error for argument '.$name.': ', $this->verbose ? $e->getCause() : $e->getCause()->compoundMessage());
          return 2;
        }
      }
    }

    try {
      $instance->run();
    } catch (\lang\Throwable $t) {
      self::$err->writeLine('*** ', $t->toString());
      return 70;    // EX_SOFTWARE according to sysexits.h
    }
    return 0;
  }
}
