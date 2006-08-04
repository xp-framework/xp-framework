<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ThrowsTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the @throws tag. 
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class ThrowsTaglet extends Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &Doc holder
     * @param   string kind
     * @param   string text
     * @return  &Tag
     */ 
    public function &tagFrom(&$holder, $kind, $text) {
      list($class, $condition)= explode(' ', $text);
      return new ThrowsTag($holder->root->classNamed($class), $condition);
    }

  } 
?>
