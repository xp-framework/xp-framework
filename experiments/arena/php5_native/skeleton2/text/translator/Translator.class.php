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
  interface Translator {
  
    /**
     * Translates the given sentence
     *
     * @model   static
     * @access  public
     * @param   string sentence
     * @return  string translation
     */  
    public function translate($string);

  }
?>
