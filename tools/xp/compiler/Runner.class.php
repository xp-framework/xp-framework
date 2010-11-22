<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler';

  uses(
    'io.File',
    'util.Properties',
    'lang.ResourceProvider',
    'xp.compiler.Compiler',
    'xp.compiler.CompilationProfileReader',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.diagnostic.DefaultDiagnosticListener',
    'xp.compiler.diagnostic.VerboseDiagnosticListener',
    'xp.compiler.io.FileSource',
    'xp.compiler.io.FileManager',
    'util.log.Logger',
    'util.log.ConsoleAppender'
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
   *     Use emitter, defaults to "source"
   *   </li>
   *   <li>-p [profile[,profile[,...]]]:
   *     Use compiler profiles (defaults to ["default"]) - xp/compiler/{profile}.xcp.ini
   *   </li>
   *   <li>-o [outputdir]: 
   *     Writed compiled files to outputdir (will be created if not existant)
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
      $profiles= array('default');
      $emitter= 'source';
      
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
          $compiler->setTrace(create(new LogCategory('xcc'))->withAppender(new ConsoleAppender(), $levels));
        } else if ('-e' === $args[$i]) {
          $emitter= $args[++$i];
        } else if ('-p' === $args[$i]) {
          $profiles= explode(',', $args[++$i]);
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
      
      // Setup emitter and load compiler profile configurations
      $emitter= Package::forName('xp.compiler.emit')->getPackage($emitter)->loadClass('Emitter')->newInstance();
      try {
        $reader= new CompilationProfileReader();
        foreach ($profiles as $configuration) {
          $reader->addSource(new Properties('res://xp/compiler/'.$configuration.'.xcp.ini'));
        }
        $emitter->setProfile($reader->getProfile());
      } catch (Throwable $e) {
        Console::$err->writeLine('*** Cannot load profile configuration(s) '.implode(',', $profiles).': '.$e->getMessage());
        exit(3);
      }
      
      // Compile files
      $success= $compiler->compile($files, $listener, $manager, $emitter);
      exit($success ? 0 : 1);
    }
  }
?>
