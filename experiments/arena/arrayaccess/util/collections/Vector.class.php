<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.IList');

  /**
   * (Insert class' description here)
   *
   * @purpose  purpose
   */
  class Vector extends Object implements IList {
    protected static
      $iterate = NULL;

    public
      $values  = array();

    static function __static() {
      self::$iterate= newinstance('Iterator', array(), '{
        private $i= 0, $v;
        public function on($v) { $self= new self(); $self->v= $v; return $self; }
        public function current() { return $this->v[$this->i]; }
        public function key() { return $this->i; }
        public function next() { $this->i++; }
        public function rewind() { $this->i= 0; }
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
      return self::$iterate->on($this->elements);
    }

    /**
     * = list[] overloading
     *
     * @param   int offset
     * @return  lang.Generic
     */
    public function offsetGet($offset) {
      return $this->get($offset);
    }

    /**
     * list[]= overloading
     *
     * @param   int offset
     * @param   mixed value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
    public function offsetSet($offset, $value) {
      if (is_int($offset)) {
        $this->set($offset, $value);
      } else if (NULL === $offset) {
        $this->add($value);
      } else {
        throw new IllegalArgumentException('Incorrect type '.$t.' for index');
      }
    }

    /**
     * isset() overloading
     *
     * @param   int offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return array_key_exists($offset, $this->elements);
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      unset($this->elements[$offset]);
    }
      
    /**
     * Returns the number of elements in this list.
     *
     * @return  int
     */
    public function size() {
      return sizeof($this->elements);
    }
    
    /**
     * Tests if this list has no elements.
     *
     * @return  bool
     */
    public function isEmpty() {
      return empty($this->elements);
    }
    
    /**
     * Adds an element to this list
     *
     * @param   lang.Generic element
     * @return  lang.Generic the added element
     * @throws  lang.IllegalArgumentException
     */
    public function add(Generic $element) {
      $this->elements[]= $element;
      return $element;
    }

    /**
     * Replaces the element at the specified position in this list with 
     * the specified element.
     *
     * @param   int index
     * @param   lang.Generic element
     * @return  lang.Generic the element previously at the specified position.
     */
    public function set($index, Generic $element) {
      $orig= $this->elements[$index];
      $this->elements[$index]= $element;
      return $orig;
    }
        
    /**
     * Returns the element at the specified position in this list.
     *
     * @param   int index
     * @return  lang.Generic
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function get($index) {
      if (!array_key_exists($index, $this->elements)) {
        raise('lang.IndexOutOfBoundsException', 'Index '.$index.' out of bounds');
      }
      return $this->elements[$index];
    }
    
    /**
     * Removes the element at the specified position in this list.
     * Shifts any subsequent elements to the left (subtracts one 
     * from their indices).
     *
     * @param   int index
     * @return  lang.Generic the element that was removed from the list
     */
    public function remove($index) {
      $element= $this->elements[$index];
      unset($this->elements[$index]);
      $this->elements= array_values($this->elements);
      return $element;
    }
    
    /**
     * Removes all of the elements from this list. The list will be empty 
     * after this call returns.
     *
     */
    public function clear() {
      $this->elements= array();
    }
    
    /**
     * Returns an array of this list's elements
     *
     * @return  lang.Generic[]
     */
    public function elements() {
      return array_values($this->elements);
    }
    
    /**
     * Checks if a value exists in this array
     *
     * @param   lang.Generic element
     * @return  bool
     */
    public function contains(Generic $element) {
      for ($i= 0, $s= sizeof($this->elements); $i < $s; $i++) {
        if ($this->elements[$i]->equals($element)) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Searches for the first occurence of the given argument
     *
     * @param   lang.Generic element
     * @return  int offset where the element was found or FALSE
     */
    public function indexOf(Generic $element) {
    
      // Note: array_search() does NOT work for objects:
      //
      // <snip from="ext/standard/array.c">
      // if (Z_TYPE_PP(value) == IS_OBJECT) {
      //     php_error_docref(NULL TSRMLS_CC, E_WARNING, "Wrong datatype for first argument");
      //     RETURN_FALSE;
      // }
      // </snip>
      for ($i= 0, $s= sizeof($this->elements); $i < $s; $i++) {
        if ($this->elements[$i]->equals($element)) return $i;
      }
      return FALSE;
    }

    /**
     * Searches for the last occurence of the given argument
     *
     * @param   lang.Generic element
     * @return  int offset where the element was found or FALSE
     */
    public function lastIndexOf(Generic $element) {
      for ($i= sizeof($this->elements)- 1; $i > -1; $i--) {
        if ($this->elements[$i]->equals($element)) return $i;
      }
      return FALSE;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $r= $this->getClassName().'<'.$this->class.">@{\n";
      for ($i= 0, $s= sizeof($this->elements); $i < $s; $i++) {
        $r.= '  '.$i.': '.str_replace("\n", "\n  ", xp::stringOf($this->elements[$i]))."\n";
      } 
      return $r.'}';
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @param   lang.Generic collection
     * @return  bool
     */
    public function equals($cmp) {
      if (
        !is('IList', $cmp) || 
        $this->size != $cmp->size
      ) return FALSE;
      
      // Compare element by element
      for ($i= 0, $s= sizeof($this->elements); $i < $s; $i++) {
        if ($this->elements[$i]->equals($cmp->elements[$i])) continue;
        return FALSE;
      }
      return TRUE;
    }
  }
?>
