<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Generic context resource
   *
   * @purpose  Base class
   */
  class ContextResource extends Object {
    var
      $values   = array();

    /**
     * Set a value
     *
     * @access  public
     * @param   string name
     * @param   &mixed data
     */
    function setValue($name, &$data) {
      $this->values[$name]= &$data;
    }

    /**
     * Get a value
     *
     * @access  public
     * @param   string name
     * @return  &mixed
     */
    function &getValue($name) {
      return $this->values[$name];
    }
    
    /**
     * Insert status
     *
     * @access  public
     * @param   &xml.Node elem
     */
    function insertStatus(&$elem) { }
  }
?>
