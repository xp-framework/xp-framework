<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Tag', 'text.doclet.Taglet');

  /**
   * A taglet that represents simple tags.
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class SimpleTaglet extends Object implements Taglet {
     
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
      return new Tag($kind, $text);
    }

  } 
?>
