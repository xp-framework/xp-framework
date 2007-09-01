<?php
/* This class is part of the XP framework
 *
 * $Id: Taglet.class.php 9104 2007-01-03 17:13:06Z friebe $ 
 */

  namespace text::doclet;

  /**
   * The interface for a custom tag used by Doclets.
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Interface
   */
  interface Taglet {
  
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text);

  }
?>
