<?php
/* This class is part of the XP framework
 *
 * $Id: SimpleTaglet.class.php 9104 2007-01-03 17:13:06Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.Tag', 'text.doclet.Taglet');

  /**
   * A taglet that represents simple tags.
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class SimpleTaglet extends lang::Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      return new Tag($kind, $text);
    }
  } 
?>
