<?php
/* This class is part of the XP framework
 *
 * $Id: MarkupProcessor.class.php 9228 2007-01-10 17:19:07Z friebe $
 */

  namespace text::doclet::markup;

  /**
   * Markup processor
   *
   * @see      xp://text.doclet.markup.MarkupBuilder
   * @purpose  Base class
   */
  class MarkupProcessor extends lang::Object {
    
    /**
     * Initializes the processor. Returns an empty string in this default
     * implementation.
     *
     * @return  string
     */
    public function initialize() {
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
