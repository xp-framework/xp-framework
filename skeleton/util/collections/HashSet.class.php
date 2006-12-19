<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.collections.HashProvider', 'util.collections.Set');

  /**
   * A set of objects
   *
   * @purpose  Interface
   */
  class HashSet extends Object implements Set {
    public
      $_elements = array(),
      $_hash     = 0;
    
    /**
     * Adds an object
     *
     * @access  public
     * @param   &lang.Object object
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    public function add(&$object) { 
      $h= $object->hashCode();
      if (isset($this->_elements[$h])) return FALSE;
      
      $this->_hash+= HashProvider::hashOf($h);
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
    public function remove(&$object) { 
      $h= $object->hashCode();
      if (!isset($this->_elements[$h])) return FALSE;

      $this->_hash-= HashProvider::hashOf($h);
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
    public function contains(&$object) { 
      return isset($this->_elements[$object->hashCode()]);
    }

    /**
     * Returns this set's size
     *
     * @access  public
     * @return  int
     */
    public function size() { 
      return sizeof($this->_elements);
    }

    /**
     * Removes all of the elements from this set
     *
     * @access  public
     */
    public function clear() { 
      $this->_elements= array();
      $this->_hash= 0;
    }

    /**
     * Returns whether this set is empty
     *
     * @access  public
     * @return  bool
     */
    public function isEmpty() {
      return 0 == sizeof($this->_elements);
    }
    
    /**
     * Adds an array of objects
     *
     * @access  public
     * @param   lang.Object[] objects
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    public function addAll($objects) { 
      $result= FALSE;
      for ($i= 0, $s= sizeof($objects); $i < $s; $i++) {
        $h= $objects[$i]->hashCode();
        if (isset($this->_elements[$h])) continue;
        
        $result= TRUE;
        $this->_hash+= HashProvider::hashOf($h);
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
    public function toArray() { 
      return array_values($this->_elements);
    }

    /**
     * Returns a hashcode for this set
     *
     * @access  public
     * @return  string
     */
    public function hashCode() {
      return $this->_hash;
    }
    
    /**
     * Returns true if this set equals another set.
     *
     * @access  public
     * @param   &lang.Object cmp
     * @return  bool
     */
    public function equals(&$cmp) {
      return (
        is('util.collections.Set', $cmp) && 
        ($this->hashCode() === $cmp->hashCode())
      );
    }

    /**
     * Returns a string representation of this set
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'['.sizeof($this->_elements).'] {';
      if (0 == sizeof($this->_elements)) return $s.' }';

      $s.= "\n";
      foreach (array_keys($this->_elements) as $key) {
        $s.= '  '.$this->_elements[$key]->toString().",\n";
      }
      return substr($s, 0, -2)."\n}";
    }

  } 
?>
