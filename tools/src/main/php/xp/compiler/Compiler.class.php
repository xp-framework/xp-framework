<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.emit.Emitter',
    'xp.compiler.task.CompilationTask',
    'xp.compiler.diagnostic.DiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.io.Source',
    'io.File'
  );

  /**
   * Compiler
   *
   */
  class Compiler extends Object implements Traceable {
    protected $cat= NULL;
  
    /**
     * Compile a set of files
     *
     * @param   xp.compiler.io.Source[] sources
     * @param   xp.compiler.diagnostic.DiagnosticListener listener
     * @param   xp.compiler.io.FileManager manager
     * @param   xp.compiler.emit.Emitter emitter
     * @return  bool success if all files compiled correctly, TRUE, FALSE otherwise
     */
    public function compile(array $sources, DiagnosticListener $listener, FileManager $manager, Emitter $emitter) {
      $emitter->setTrace($this->cat);
      $listener->runStarted();
      $errors= 0;
      $done= create('new util.collections.HashTable<xp.compiler.io.Source, xp.compiler.types.Types>()');
      foreach ($sources as $source) {
        try {
          create(new CompilationTask($source, $listener, $manager, $emitter, $done))->run();
        } catch (CompilationException $e) {
          $errors++;
        }
      }
      $listener->runFinished();
      return 0 === $errors;
    }
    
    /**
     * Set log category for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }
  }
?>
