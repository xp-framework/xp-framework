<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * ArrayList
   *
   * @purpose  Wrapper
   */
  class ArrayList extends Object implements ArrayAccess {
    protected
      $items= array();

    /**
     * Checks whether a specified index exists
     *
     * @access  public
     * @param   mixed index
     * @return  bool
     */
    function offsetExists($index) {
      return array_key_exists($index, $this->items);
    }

    /**
     * Retrieves a value by a specified index
     *
     * @access  public
     * @param   mixed index
     * @return  mixed
     */
    function offsetGet($index) {
      return $this->items[$index];
    }

    /**
     * Sets a value by a specified index
     *
     * @access  public
     * @param   mixed index
     * @param   mixed value
     */
    function offsetSet($index, $value) {
      if (is_null($index)) {
        $this->items[]= $value;
      } else {
        $this->items[$index]= $value;
      }
    }

    /**
     * Unsets a value by a specified index
     *
     * @access  public
     * @param   mixed index
     */
    function offsetUnset($index) {
      unset($this->items[$index]);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return (
        $this->getClassName().'['.sizeof($this->items).']@{'.
        substr(var_export($this->items, 1), 7, -1).
        '}'
      );
    }
  }
?>
