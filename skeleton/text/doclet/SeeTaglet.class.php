<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('SeeTag');

  /**
   * A taglet that represents the @see tag. 
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class SeeTaglet extends Object {
  
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
      return new SeeTag($kind, $text);
    }

  } implements(__FILE__, 'Taglet');
?>
