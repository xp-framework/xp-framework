<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a "numeric" array
   *
   * @purpose  Wrapper
   */
  class ArrayList extends Object implements ArrayAccess, IteratorAggregate {
    protected
      $iterator = NULL;

    public
      $values   = NULL,
      $length   = 0;

    /**
     * Create a new instance of an ArrayList
     *
     * @param   mixed arg either mixed[] => values or an int => size
     * @return  lang.types.ArrayList
     */
    public static function newInstance($arg) {
      if (is_array($arg)) {
        $self= new self();
        $self->values= array_values($arg);
        $self->length= sizeof($self->values);
      } else {
        $self= new self();
        $self->length= (int)$arg;
      }
      return $self;
    }

    /**
     * Returns a hashcode for this number
     *
     * @return  string
     */
    public function hashCode() {
      return $this->length.'['.serialize($this->values);
    }
    
    /**
     * Constructor
     *
     * @param   mixed* values
     */
    public function __construct() {
      if (0 != ($this->length= func_num_args())) {
        $this->values= func_get_args();
      }
    }
    
    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      if (!$this->iterator) $this->iterator= newinstance('Iterator', array($this), '{
        private $i= 0, $v;
        public function __construct($v) { $this->v= $v; }
        public function current() { return $this->v->values[$this->i]; }
        public function key() { return $this->i; }
        public function next() { $this->i++; }
        public function rewind() { $this->i= 0; }
        public function valid() { return $this->i < $this->v->length; }
      }');
      return $this->iterator;
    }

    /**
     * = list[] overloading
     *
     * @param   int offset
     * @return  mixed
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function offsetGet($offset) {
      if ($offset >= $this->length || $offset < 0) {
        raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
      }
      return $this->values[$offset];
    }

    /**
     * list[]= overloading
     *
     * @param   int offset
     * @param   mixed value
     * @throws  lang.IllegalArgumentException if key is neither numeric (set) nor NULL (add)
     */
    public function offsetSet($offset, $value) {
      if (!is_int($offset)) {
        throw new IllegalArgumentException('Incorrect type '.gettype($offset).' for index');
      }
      
      if ($offset >= $this->length || $offset < 0) {
        raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
      }
      $this->values[$offset]= $value;
    }

    /**
     * isset() overloading
     *
     * @param   int offset
     * @return  bool
     */
    public function offsetExists($offset) {
      return ($offset >= 0 && $offset < $this->length);
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      throw new IllegalArgumentException('Cannot remove from immutable list');
    }
    
    /**
     * Helper method to compare two arrays recursively
     *
     * @param   array a1
     * @param   array a2
     * @return  bool
     */
    protected function arrayequals($a1, $a2) {
      if (sizeof($a1) != sizeof($a2)) return FALSE;

      foreach (array_keys((array)$a1) as $k) {
        switch (TRUE) {
          case !array_key_exists($k, $a2): 
            return FALSE;

          case is_array($a1[$k]):
            if (!$this->arrayequals($a1[$k], $a2[$k])) return FALSE;
            break;

          case is('Generic', $a1[$k]):
            if (!$a1[$k]->equals($a2[$k])) return FALSE;
            break;

          case $a1[$k] !== $a2[$k]:
            return FALSE;
        }
      }
      return TRUE;
    }
    
    /**
     * Checks whether a given object is equal to this arraylist
     *
     * @param   lang.Object cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->arrayequals($this->values, $cmp->values);
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return (
        $this->getClassName().'['.sizeof($this->values)."]@{".
        implode(', ', array_map(array('xp', 'stringOf'), $this->values)).
        '}'
      );
    }
  }
?>
