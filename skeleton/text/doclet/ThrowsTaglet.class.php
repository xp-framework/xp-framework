<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ThrowsTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the throws tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ThrowsTaglet extends Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      sscanf($text, '%s %[^$]', $class, $condition);
      return new ThrowsTag($holder->root->classNamed($class), (string)$condition);
    }
  } 
?>
