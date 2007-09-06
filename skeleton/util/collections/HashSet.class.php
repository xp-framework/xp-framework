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
   * @purpose  Set implemenentation
   */
  class HashSet extends Object implements Set {
    protected static
      $iterate   = NULL;

    protected
      $_elements = array(),
      $_hash     = 0;

    public
      $__generic = array();

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
     * @param   mixed value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
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
     * @param   int offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return $this->contains($offset);
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      $this->remove($offset);
    }
    
    /**
     * Adds an object
     *
     * @param   lang.Generic object
     * @return  bool TRUE if this set did not already contain the specified element. 
     */
    public function add(Generic $object) { 
      if ($this->__generic && !$object instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Object '.xp::stringOf($object).' must be of '.$this->__generic[0]);
      }
      $h= $object->hashCode();
      if (isset($this->_elements[$h])) return FALSE;
      
      $this->_hash+= HashProvider::hashOf($h);
      $this->_elements[$h]= $object;
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @param   lang.Generic object
     * @return  bool TRUE if this set contained the specified element. 
     */
    public function remove(Generic $object) { 
      if ($this->__generic && !$object instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Object '.xp::stringOf($object).' must be of '.$this->__generic[0]);
      }
      $h= $object->hashCode();
      if (!isset($this->_elements[$h])) return FALSE;

      $this->_hash-= HashProvider::hashOf($h);
      unset($this->_elements[$h]);
      return TRUE;
    }

    /**
     * Removes an object from this set
     *
     * @param   lang.Generic object
     * @return  bool TRUE if the set contains the specified element. 
     */
    public function contains(Generic $object) { 
      if ($this->__generic && !$object instanceof $this->__generic[0]) {
        throw new IllegalArgumentException('Object '.xp::stringOf($object).' must be of '.$this->__generic[0]);
      }
      return isset($this->_elements[$object->hashCode()]);
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
     * @param   lang.Generic[] objects
     * @return  bool TRUE if this set changed as a result of the call. 
     */
    public function addAll($objects) { 
      $result= FALSE;
      for ($i= 0, $s= sizeof($objects); $i < $s; $i++) {
        if ($this->__generic && !$objects[$i] instanceof $this->__generic[0]) {
          throw new IllegalArgumentException('Object #'.$i.' '.xp::stringOf($objects[$i]).' must be of '.$this->__generic[0]);
        }
        $h= $objects[$i]->hashCode();
        if (isset($this->_elements[$h])) continue;
        
        $result= TRUE;
        $this->_hash+= HashProvider::hashOf($h);
        $this->_elements[$h]= $objects[$i];
      }
      return $result;
    }

    /**
     * Returns an array containing all of the elements in this set. 
     *
     * @return  lang.Generic[] objects
     */
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
      return $cmp instanceof self && $this->hashCode() === $cmp->hashCode();
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
