<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.streams.OutputStreamWriter', 
    'util.profiling.Timer', 
    'xp.compiler.diagnostic.DiagnosticListener'
  );

  /**
   * Default DiagnosticListener implementation
   *
   * @see   xp://xp.compiler.diagnostic.DiagnosticListener
   */
  class VerboseDiagnosticListener extends Object implements DiagnosticListener {
    protected $writer= NULL;

    /**
     * (Insert method's description here)
     *
     * @param   io.streams.OutputStreamWriter writer
     */
    public function __construct(OutputStreamWriter $writer) {
      $this->writer= $writer;
      $this->timer= new Timer();
    }

    /**
     * Called when compilation starts
     *
     * @param   xp.compiler.io.Source src
     */
    public function compilationStarted(xp·compiler·io·Source $src) {
      $this->writer->writeLine($src);
    }
  
    /**
     * Called when a compilation finishes successfully.
     *
     * @param   xp.compiler.io.Source src
     * @param   io.File compiled
     * @param   string[] messages
     */
    public function compilationSucceeded(xp·compiler·io·Source $src, File $compiled, array $messages= array()) {
      if ($messages) {
        foreach ($messages as $message) {
          $this->writer->writeLine('  ', $message);
        }
      }
      $this->writer->writeLine($src, ': OK');
    }
    
    /**
     * Called when parsing fails
     *
     * @param   xp.compiler.io.Source src
     * @param   text.parser.generic.ParseException reason
     */
    public function parsingFailed(xp·compiler·io·Source $src, ParseException $reason) {
      $this->writer->writeLine($src, ': ', $reason->compoundMessage());
      $reason->printStackTrace();
    }

    /**
     * Called when emitting fails
     *
     * @param   xp.compiler.io.Source src
     * @param   lang.FormatException reason
     */
    public function emittingFailed(xp·compiler·io·Source $src, FormatException $reason) {
      $this->writer->writeLine($src, ': ', $reason->compoundMessage());
      $reason->printStackTrace();
    }

    /**
     * Called when compilation fails
     *
     * @param   xp.compiler.io.Source src
     * @param   lang.Throwable reason
     */
    public function compilationFailed(xp·compiler·io·Source $src, Throwable $reason) {
      $this->writer->writeLine($src, ': ', $reason->compoundMessage());
      $reason->printStackTrace();
    }

    /**
     * Called when a run starts.
     *
     */
    public function runStarted() {
      $this->timer->start();
    }
    
    /**
     * Called when a test run finishes.
     *
     */
    public function runFinished() {
      $this->timer->stop();

      // Summary
      $this->writer->writeLinef(
        'Memory used: %.2f kB (%.2f kB peak)',
        memory_get_usage(TRUE) / 1024,
        memory_get_peak_usage(TRUE) / 1024
      );
      $this->writer->writeLinef(
        'Time taken: %.2f seconds (%.3f avg)',
        $this->timer->elapsedTime(),
        $this->timer->elapsedTime() / $this->started
      );
    }
  }
?>
