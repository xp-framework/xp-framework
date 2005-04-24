<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('ReturnTag');

  /**
   * A taglet that represents the @return tag. 
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class ReturnTaglet extends Object {
     
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
      list($type, $label)= explode(' ', $text, 2);
      return new ReturnTag($type, $label);
    }

  } implements(__FILE__, 'Taglet');
?>
