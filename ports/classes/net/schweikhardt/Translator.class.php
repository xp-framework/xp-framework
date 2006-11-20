<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Translator interface
   *
   * @purpose  Interface
   */
  class Translator extends Interface {
  
    /**
     * Translates the given sentence
     *
     * @model   static
     * @access  public
     * @param   string sentence
     * @return  string translation
     */  
    function translate($string) { }

  }
?>
