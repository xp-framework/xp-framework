<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ThrowsTag');

  /**
   * A taglet that represents the throws tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ThrowsTaglet extends Object {
     
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  &text.doclet.Tag
     */ 
    function &tagFrom(&$holder, $kind, $text) {
      list($class, $condition)= explode(' ', $text);
      return new ThrowsTag($holder->root->classNamed($class), $condition);
    }

  } implements(__FILE__, 'text.doclet.Taglet');
?>
