<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Tag');

  /**
   * A taglet that represents simple tags.
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class SimpleTaglet extends Object {
     
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &Doc holder
     * @param   string kind
     * @param   string text
     * @return  &Tag
     */ 
    function &tagFrom(&$holder, $kind, $text) {
      return new Tag($kind, $text);
    }

  } implements(__FILE__, 'Taglet');
?>
