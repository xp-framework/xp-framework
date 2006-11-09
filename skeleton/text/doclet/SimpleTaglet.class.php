<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Tag');

  /**
   * A taglet that represents simple tags.
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class SimpleTaglet extends Object {
     
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
      return new Tag($kind, $text);
    }

  } implements(__FILE__, 'text.doclet.Taglet');
?>
