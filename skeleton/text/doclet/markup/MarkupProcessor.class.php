<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Markup processor
   *
   * @see      xp://text.doclet.markup.MarkupBuilder
   * @purpose  Base class
   */
  abstract class MarkupProcessor extends Object {
    
    /**
     * Initializes the processor. Returns an empty string in this default
     * implementation.
     *
     * @param   array<string, string> attributes
     * @return  string
     */
    public function initialize($attributes= array()) {
      return '';
    }
    
    /**
     * Process a token. Returns an empty string in this default
     * implementation.
     *
     * @param   string token
     * @return  string
     */
    public function process($token) {
      return '';
    }
    
    /**
     * Finalizes the processor. Returns an empty string in this default
     * implementation.
     *
     * @return  string
     */
    public function finalize() {
      return '';
    }
  }
?>
