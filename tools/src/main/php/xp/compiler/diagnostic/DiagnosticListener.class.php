<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Classes implementing this interface listen to the compilation 
   * process.
   *
   */
  interface DiagnosticListener {

    /**
     * Called when compilation starts
     *
     * @param   xp.compiler.io.Source
     */
    public function compilationStarted(xp·compiler·io·Source $src);
  
    /**
     * Called when a compilation finishes successfully.
     *
     * @param   xp.compiler.io.Source
     * @param   io.File compiled
     * @param   string[] messages
     */
    public function compilationSucceeded(xp·compiler·io·Source $src, File $compiled, array $messages= array());
    
    /**
     * Called when parsing fails
     *
     * @param   xp.compiler.io.Source
     * @param   text.parser.generic.ParseException reason
     */
    public function parsingFailed(xp·compiler·io·Source $src, ParseException $reason);

    /**
     * Called when emitting fails
     *
     * @param   xp.compiler.io.Source
     * @param   lang.FormatException reason
     */
    public function emittingFailed(xp·compiler·io·Source $src, FormatException $reason);

    /**
     * Called when compilation fails
     *
     * @param   xp.compiler.io.Source
     * @param   lang.Throwable reason
     */
    public function compilationFailed(xp·compiler·io·Source $src, Throwable $reason);

    /**
     * Called when a run starts.
     *
     */
    public function runStarted();
    
    /**
     * Called when a test run finishes.
     *
     */
    public function runFinished();
  }
?>
