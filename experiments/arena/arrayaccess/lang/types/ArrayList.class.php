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
    protected static
      $iterate = NULL;

    public
      $values  =  NULL;

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
     * Constructor
     *
     * @param   mixed[] values default array()
     */
    public function __construct($values= array()) {
      $this->values= array_values($values);
    }
    
    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      return self::$iterate->on($this->values);
    }

    /**
     * = list[] overloading
     *
     * @param   int offset
     * @return  mixed
     * @throws  lang.IndexOutOfBoundsException if key does not exist
     */
    public function offsetGet($offset) {
      if (!array_key_exists($offset, $this->values)) {
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
      if (is_int($offset)) {
        if ($offset < 0 || $offset > sizeof($this->values)) {
          raise('lang.IndexOutOfBoundsException', 'Offset '.$offset.' out of bounds');
        }
        $this->values[$offset]= $value;
      } else if (NULL === $offset) {
        $this->values[]= $value;
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
      return array_key_exists($offset, $this->values);
    }

    /**
     * unset() overloading
     *
     * @param   int offset
     */
    public function offsetUnset($offset) {
      unset($this->values[$offset]);
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

      foreach (array_keys($a1) as $k) {
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
