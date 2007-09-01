<?php
/* This class is part of the XP framework
 *
 * $Id: Translator.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::schweikhardt;

  /**
   * Translator interface
   *
   * @purpose  Interface
   */
  interface Translator {
  
    /**
     * Translates the given sentence
     *
     * @param   string sentence
     * @return  string translation
     */  
    public static function translate($string);

  }
?>
