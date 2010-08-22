<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.collections.HashProvider', 'util.collections.Set');

  /**
   * A set of objects
   *
   * @test     xp://net.xp_framework.unittest.util.collections.HashSetTest
   * @test     xp://net.xp_framework.unittest.util.collections.GenericsTest
   * @test     xp://net.xp_framework.unittest.util.collections.ArrayAccessTest
   * @purpose  Set implemenentation
   */
  #[@generic(self= 'T', Set= 'T')]
  class HashSet extends Object implements Set {
    protected static
      $iterate   = NULL;

    protected
      $_elements = array(),
      $_hash     = 0;

    static function __static() {
      self::$iterate= newinstance('Iterator', array(), '{
        private $i= 0, $v;
        public function on($v) { $self= new self(); $self->v= $v; return $self; }
        public function current() { return current($this->v); }
        public function key() { return $this->i; }
        public function next() { next($this->v); $this->i++; }
        public function rewind() { reset($this->v); $this->i= 0; }
        public function valid() { return $this->i < sizeof($this->v); }
      }');
    }

    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      return self::$iterate->on($this->_elements);
    }

    /**
     * = list[] overloading
     *
     * @param   int offset
     * @return  lang.Generic
     */
    public function offsetGet($offset) {
      throw new IllegalArgumentException('Unsupported operation');
    }

    /**
     * list[]= overloading
     *
     * @param   int offset
     * @param   T value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
    #[@generic(params= ', T')]
    public function offsetSet($offset, $value) {
       if (NULL === $offset) {
        $this->add($value);
      } else {
        throw new IllegalArgumentException('Unsupported operation');
      }
    }

    /**
     * isset() overloading
     *
     * @param   T offset
     * @return  bool
     */
    #[@generic(params= 'T')]
    public function offsetExists($offset) {
      return $this->contains($offset);
    }

    /**
     * unset() overloading
     *
     * @param   T offset
     */
    #[@generic(params= 'T')]
    public function offsetUnset($offset) {
      $this->remove($offset);
    }
    
    /**
     * Adds an object
     *
     * @param   T element
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    #[@generic(params= 'T')]
    public function add($element) { 
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      if (isset($this->_elements[$h])) return FALSE;
      
      $this->_hash+= HashProvider::hashOf($h);
      $this->_elements[$h]= $element;
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @param   T element
     * @return  bool TRUE if this set contained the specified element. 
     */
    #[@generic(params= 'T')]
    public function remove($element) { 
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      if (!isset($this->_elements[$h])) return FALSE;

      $this->_hash-= HashProvider::hashOf($h);
      unset($this->_elements[$h]);
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @param   T element
     * @return  bool TRUE if the set contains the specified element. 
     */
    #[@generic(params= 'T')]
    public function contains($element) { 
      $h= $element instanceof Generic ? $element->hashCode() : $element;
      return isset($this->_elements[$h]);
    }

    /**
     * Returns this set's size
     *
     * @return  int
     */
    public function size() { 
      return sizeof($this->_elements);
    }

    /**
     * Removes all of the elements from this set
     *
     */
    public function clear() { 
      $this->_elements= array();
      $this->_hash= 0;
    }

    /**
     * Returns whether this set is empty
     *
     * @return  bool
     */
    public function isEmpty() {
      return 0 == sizeof($this->_elements);
    }
    
    /**
     * Adds an array of objects
     *
     * @param   T[] elements
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    #[@generic(params= 'T[]')]
    public function addAll($elements) { 
      $changed= FALSE;
      foreach ($elements as $element) {
        $h= $element instanceof Generic ? $element->hashCode() : $element;
        if (isset($this->_elements[$h])) continue;

        $this->_hash+= HashProvider::hashOf($h);
        $this->_elements[$h]= $element;
        $changed= TRUE;
      }
      return $changed;
    }

    /**
     * Returns an array containing all of the elements in this set. 
     *
     * @return  T[] objects
     */
    #[@generic(return= 'T[]')]
    public function toArray() { 
      return array_values($this->_elements);
    }

    /**
     * Returns a hashcode for this set
     *
     * @return  string
     */
    public function hashCode() {
      return $this->_hash;
    }
    
    /**
     * Returns true if this set equals another set.
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self && 
        $this->__generic === $cmp->__generic &&
        $this->_hash === $cmp->_hash
      );
    }

    /**
     * Returns a string representation of this set
     *
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
