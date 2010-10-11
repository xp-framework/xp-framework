<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler';

  uses(
    'io.File',
    'xp.compiler.Compiler',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.diagnostic.DefaultDiagnosticListener',
    'xp.compiler.diagnostic.VerboseDiagnosticListener',
    'xp.compiler.io.FileSource',
    'xp.compiler.io.FileManager',
    'util.log.Logger',
    'util.log.LogAppender'
  );

  /**
   * XP Compiler
   *
   * Usage:
   * <pre>
   * $ xcc [options] [file [file [... ]]]
   * </pre>
   *
   * Options is one of:
   * <ul>
   *   <li>-v:
   *     Display verbose diagnostics
   *   </li>
   *   <li>-cp [path]: 
   *     Add path to classpath
   *   </li>
   *   <li>-sp [path]: 
   *     Adds path to source path (source path will equal classpath initially)
   *   </li>
   *   <li>-e [emitter]: 
   *     Use emitter, one of "oel" or "source", defaults to "source"
   *   </li>
   *   <li>-o [outputdir]: 
   *     Writed compiled files to outputdir (will be created if not existant)
   *   </li>
   *   <li>-O [optimization[,optimization[...]]]: 
   *     Load and install the given optimizations (each optimization may
   *     be either a fully qualified class name or a package reference,
   *     e.g. "xp.compiler.optimize.*")
   *   </li>
   *   <li>-t [level[,level[...]]]:
   *     Set trace level (all, none, info, warn, error, debug)
   *   </li>
   * </ul>
   *
   * @purpose  Runner
   */
  class xp·compiler·Runner extends Object {
  
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
     * Shows usage and exits
     *
     */
    protected function showUsage() {
      Console::$err->writeLine(self::textOf(XPClass::forName(xp::nameOf(__CLASS__))->getComment()));
      exit(2);
    }
    
    /**
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      if (empty($args)) self::showUsage();
      
      $compiler= new Compiler();
      $manager= new FileManager();
      $manager->setSourcePaths(xp::$registry['classpath']);
      $emitter= 'source';
      $optimization= 'xp.compiler.optimize.Optimization';
      $optimizations= array();
      
      // Handle arguments
      $files= array();
      $listener= new DefaultDiagnosticListener(Console::$out);
      for ($i= 0, $s= sizeof($args); $i < $s; $i++) {
        if ('-?' === $args[$i] || '--help' === $args[$i]) {
          self::showUsage();
        } else if ('-cp' === $args[$i]) {
          ClassLoader::registerPath($args[++$i]);
        } else if ('-sp' === $args[$i]) {
          $manager->addSourcePath($args[++$i]);
        } else if ('-v' === $args[$i]) {
          $listener= new VerboseDiagnosticListener(Console::$out);
        } else if ('-t' === $args[$i]) {
          $levels= LogLevel::NONE;
          foreach (explode(',', $args[++$i]) as $level) {
            $levels |= LogLevel::named($level);
          }
          $appender= newinstance('util.log.Appender', array(), '{
            public function append(LoggingEvent $event) {
              Console::$err->write($this->layout->format($event));
            }
          }');
          $compiler->setTrace(Logger::getInstance()->getCategory()->withAppender($appender, $levels));
        } else if ('-e' === $args[$i]) {
          $emitter= $args[++$i];
        } else if ('-O' === $args[$i]) {
          foreach (explode(',', $args[++$i]) as $reference) {
            if ($p= strpos($reference, '.*')) {
              foreach (Package::forName(substr($reference, 0, $p))->getClasses() as $class) {
                if (
                  $class->isInterface() || 
                  MODIFIER_PUBLIC != $class->getModifiers() || 
                  !$class->isSubclassOf($optimization)
                ) continue;
                $optimizations[]= $class->newInstance();
              }
            } else {
              $class= XPClass::forName($reference);
              if (!$class->isSubclassOf($optimization)) {
                Console::$err->writeLine('*** Class ', $class, ' is not an optimization, ignoring');
              } else {
                $optimizations[]= $class->newInstance();
              }
            }
          }
        } else if ('-o' === $args[$i]) {
          $output= $args[++$i];
          $folder= new Folder($output);
          $folder->exists() || $folder->create();
          $manager->setOutput($folder);
          ClassLoader::registerPath($output);
        } else {
          $files[]= new FileSource(new File($args[$i]));
        }
      }
      
      // Check
      if (empty($files)) {
        Console::$err->writeLine('*** No files given (-? will show usage)');
        exit(2);
      }
      
      // Setup emitter and optimizations
      $emitter= Package::forName('xp.compiler.emit')->getPackage($emitter)->loadClass('Emitter')->newInstance();
      foreach ($optimizations as $optimization) {
        $emitter->addOptimization($optimization);
      }

      // Add errors
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.IsAssignable')->newInstance(), TRUE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.MemberRedeclarationCheck')->newInstance(), TRUE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.RoutinesVerification')->newInstance(), TRUE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.FieldsVerification')->newInstance(), TRUE);
      
      // Add warnings
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.TypeHasDocumentation')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.TypeMemberHasDocumentation')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.ConstantsAreDiscouraged')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.UninitializedVariables')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.MethodCallVerification')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.MemberAccessVerification')->newInstance(), FALSE);
      $emitter->addCheck(XPClass::forName('xp.compiler.checks.ArrayAccessVerification')->newInstance(), FALSE);
      
      // Compile files
      $success= $compiler->compile($files, $listener, $manager, $emitter);
      exit($success ? 0 : 1);
    }
  }
?>
