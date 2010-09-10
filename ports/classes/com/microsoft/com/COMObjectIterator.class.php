<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.microsoft.com.COMObject');

  /**
   * COM Object Iterator
   *
   * @see      xp://com.microsoft.com.COMObject#getIterator
   * @ext      com
   * @platform Windows
   */
  class COMObjectIterator extends Object implements Iterator {
    protected $h= NULL;
  
    /**
     * Constructor
     *
     * @param   var handle
     */    
    public function __construct($handle) {
      $this->h= $handle;
    }

    /**
     * Returns current value of iteration
     *
     * @return  var
     */
    public function current() { 
      $v= current($this->h);
      if ($v instanceof COM || $v instanceof Variant) {
        return new COMObject($v);
      } else {
        return $v;
      }
    }

    /**
     * Returns current offset of iteration
     *
     * @return  int
     */
    public function key() { 
      return key($this->h);
    }

    /**
     * Returns current value of iteration
     *
     */
    public function next() { 
      next($this->h);
    }

    /**
     * Returns current value of iteration
     *
     */
    public function rewind() { 
      reset($this->h);
    }
    
    /**
     * Checks whether iteration should continue
     *
     * @return  bool
     */
    public function valid() { 
      return NULL !== key($this->h);
    }
  }
?>
