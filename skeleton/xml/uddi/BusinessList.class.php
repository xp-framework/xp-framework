<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.uddi.Business');

  /**
   * List of businesses
   *
   * @see      xp://xml.uddi.FindBusinessesCommand
   * @purpose  Return wrapper
   */
  class BusinessList extends Object {
    var 
      $operator  = '',
      $truncated = FALSE,
      $items     = array();

    /**
     * Set Operator
     *
     * @access  public
     * @param   string operator
     */
    function setOperator($operator) {
      $this->operator= $operator;
    }

    /**
     * Get Operator
     *
     * @access  public
     * @return  string
     */
    function getOperator() {
      return $this->operator;
    }
      
    /**
     * Set truncated
     *
     * @access  public
     * @param   bool truncated
     */
    function setTruncated($truncated) {
      $this->truncated= $truncated;
    }

    /**
     * Retrieve if the list is truncated
     *
     * @access  public
     * @return  bool
     */
    function isTruncated() {
      return $this->truncated;
    }
    
    /**
     * Returns number of items in this list
     *
     * @access  public
     * @return  int
     */
    function numItems() {
      return sizeof($this->items);
    }
    
    /**
     * Retrieve the business item at a specified position
     *
     * @access  public
     * @param   int pos the position, starting from 0 to numItems() - 1
     * @return  &xml.uddi.Business or NULL if there is no such item
     */
    function &itemAt($pos) {
      if (isset($this->items[$pos])) return $this->items[$pos]; else return NULL;
    }

    /**
     * Create a string representation of this list
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sizeof($this->items);
      $r= sprintf(
        "%s (operator='%s',#items=%d,truncated=%s)@{\n",
        $this->getClassName(),
        $this->operator,
        $s,
        var_export($this->truncated, 1)
      );
      for ($i= 0; $i < $s; $i++) {
        $r.= '  '.str_replace("\n", "\n  ", $this->items[$i]->toString())."\n";
      }
      return $r.= '}';
    }
  }
?>
