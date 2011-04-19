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
  class DefaultDiagnosticListener extends Object implements DiagnosticListener {
    protected
      $writer    = NULL,
      $started   = 0,
      $failed    = 0,
      $succeeded = 0,
      $timer     = NULL,
      $messages  = array();
    
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
      $this->started++;
    }
  
    /**
     * Called when a compilation finishes successfully.
     *
     * @param   xp.compiler.io.Source src
     * @param   io.File compiled
     * @param   string[] messages
     */
    public function compilationSucceeded(xp·compiler·io·Source $src, File $compiled, array $messages= array()) {
      $this->writer->write('.');
      $this->succeeded++;
      if (!empty($messages)) {
        $this->messages[$src->getURI()]= $messages;
      }
    }
    
    /**
     * Called when parsing fails
     *
     * @param   xp.compiler.io.Source src
     * @param   text.parser.generic.ParseException reason
     */
    public function parsingFailed(xp·compiler·io·Source $src, ParseException $reason) {
      $this->writer->write('P');
      $this->failed++;
      $this->messages[$src->getURI()]= $reason->getCause()->compoundMessage();
    }

    /**
     * Called when emitting fails
     *
     * @param   xp.compiler.io.Source src
     * @param   lang.FormatException reason
     */
    public function emittingFailed(xp·compiler·io·Source $src, FormatException $reason) {
      $this->writer->write('E');
      $this->failed++;
      $this->messages[$src->getURI()]= $reason->compoundMessage();
    }

    /**
     * Called when compilation fails
     *
     * @param   xp.compiler.io.Source src
     * @param   lang.Throwable reason
     */
    public function compilationFailed(xp·compiler·io·Source $src, Throwable $reason) {
      $this->writer->write('F');
      $this->failed++;
      $this->messages[$src->getURI()]= $reason->compoundMessage();
    }

    /**
     * Called when a run starts.
     *
     */
    public function runStarted() {
      $this->failed= $this->succeeded= $this->started= 0;
      $this->writer->write('[');
      $this->timer->start();
      $this->messages= array();
    }
    
    /**
     * Called when a test run finishes.
     *
     */
    public function runFinished() {
      $this->timer->stop();
      $this->writer->writeLine(']');
      $this->writer->writeLine();
      
      if (!empty($this->messages)) {
        foreach ($this->messages as $uri => $message) {
          $this->writer->writeLine('* ', basename($uri), ': ', $message);
          $this->writer->writeLine();
        }
      }
      
      // Summary
      $this->writer->writeLinef('Done: %d/%d compiled, %d failed', $this->succeeded, $this->started, $this->failed);
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
