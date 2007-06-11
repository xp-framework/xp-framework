<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.uddi.Business');

  /**
   * List of businesses
   *
   * @see      xp://webservices.uddi.FindBusinessesCommand
   * @purpose  Return wrapper
   */
  class BusinessList extends Object {
    public 
      $operator  = '',
      $truncated = FALSE,
      $items     = array();

    /**
     * Set Operator
     *
     * @param   string operator
     */
    public function setOperator($operator) {
      $this->operator= $operator;
    }

    /**
     * Get Operator
     *
     * @return  string
     */
    public function getOperator() {
      return $this->operator;
    }
      
    /**
     * Set truncated
     *
     * @param   bool truncated
     */
    public function setTruncated($truncated) {
      $this->truncated= $truncated;
    }

    /**
     * Retrieve if the list is truncated
     *
     * @return  bool
     */
    public function isTruncated() {
      return $this->truncated;
    }
    
    /**
     * Returns number of items in this list
     *
     * @return  int
     */
    public function numItems() {
      return sizeof($this->items);
    }
    
    /**
     * Retrieve the business item at a specified position
     *
     * @param   int pos the position, starting from 0 to numItems() - 1
     * @return  webservices.uddi.Business or NULL if there is no such item
     */
    public function itemAt($pos) {
      if (isset($this->items[$pos])) return $this->items[$pos]; else return NULL;
    }

    /**
     * Create a string representation of this list
     *
     * @return  string
     */
    public function toString() {
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
