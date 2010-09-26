<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.compiler.io';

  uses('io.streams.InputStream', 'xp.compiler.Syntax');

  /**
   * Represents a source object
   *
   * @see   xp://xp.compiler.io.FileManager
   */
  interface xp·compiler·io·Source {

    /**
     * Get input stream
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream();
    
    /**
     * Get syntax
     *
     * @return  xp.compiler.Syntax
     */
    public function getSyntax();

    /**
     * Get URI of this sourcefile - as source in error messages and
     * warnings.
     *
     * @return  string
     */
    public function getURI();
  }
?>
