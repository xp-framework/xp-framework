<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The interface for a custom tag used by Doclets.
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Interface
   */
  class Taglet extends Interface {
  
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  &text.doclet.Tag
     */ 
    function &tagFrom(&$holder, $kind, $text) { }

  }
?>
