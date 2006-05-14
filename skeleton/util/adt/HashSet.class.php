<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A set of objects
   *
   * @deprecated by RFC #0057
   * @purpose  Interface
   */
  class HashSet extends Object {
    var
      $_elements= array();
    
    /**
     * Adds an object
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    function add(&$object) { 
      $h= $object->hashCode();
      if (isset($this->_elements[$h])) return FALSE;
      
      $this->_elements[$h]= &$object;
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if this set contained the specified element. 
     */
    function remove(&$object) { 
      $h= $object->hashCode();
      if (!isset($this->_elements[$h])) return FALSE;

      unset($this->_elements[$h]);
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if the set contains the specified element. 
     */
    function contains(&$object) { 
      $h= $object->hashCode();
      return isset($this->_elements[$h]);
    }

    /**
     * Returns this set's size
     *
     * @access  public
     * @return  int
     */
    function size() { 
      return sizeof($this->_elements);
    }

    /**
     * Removes all of the elements from this set
     *
     * @access  public
     */
    function clear() { 
      $this->_elements= array();
    }

    /**
     * Returns whether this set is empty
     *
     * @access  public
     * @return  bool
     */
    function isEmpty() {
      return 0 == sizeof($this->_elements);
    }
    
    /**
     * Adds an array of objects
     *
     * @access  public
     * @param   lang.Object[] objects
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    function addAll($objects) { 
      $result= FALSE;
      for ($i= 0, $s= sizeof($objects); $i < $s; $i++) {
        $h= $objects[$i]->hashCode();
        if (isset($this->_elements[$h])) continue;
        
        $result= TRUE;
        $this->_elements[$h]= &$objects[$i];
      }
      return $result;
    }

    /**
     * Returns an array containing all of the elements in this set. 
     *
     * @access  public
     * @return  lang.Object[] objects
     */
    function toArray() { 
      return array_values($this->_elements);
    }

  } implements(__FILE__, 'util.adt.Set');
?>
