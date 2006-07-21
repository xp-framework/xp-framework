<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * The interface for a custom tag used by Doclets.
   *
   * @see      xp://TagletManager
   * @purpose  Interface
   */
  interface Taglet {
  
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &Doc holder
     * @param   string kind
     * @param   string text
     * @return  &Tag
     */ 
    public function &tagFrom(&$holder, $kind, $text);

  }
?>
