<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Abstract base class for appenders
   *
   */
  class LogAppender extends Object {

    /**
     * Fügt die Daten an eine Textdatei an
     *
     * @access public
     * @param  mixed args Variablen
     */ 
    function append() {
    }
 
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */   
    function finalize() {
    
    }
    
    /**
     * Gibt eine Variable schön lesbar zurück
     *
     * @access public
     * @param  mixed var Variable jeglichen Types
     * @return string Ein Printout der Variable
     */
    function varSource($var) {
      if (is_a($var, 'Object')) return $var->toString();
      return is_string($var) ? $var : var_export($var, 1);
    }
  }
?>
