<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpConnection');

  /**
   * Iterator for FtpEntryList which removes "." and ".." directories
   *
   * @see      php://language.oop5.iterations
   * @purpose  Iterator implementation
   */
  class FtpListIterator extends Object implements Iterator {
    private 
      $i= 0, 
      $v= array(), 
      $c= NULL, 
      $e= NULL;

    /**
     * Constructor
     *
     * @param   string[] v
     * @param   peer.ftp.FtpConnection c
     */
    public function __construct($v, FtpConnection $c) { 
      $this->v= $v; 
      $this->c= $c; 
    }

    /**
     * Get current entry
     *
     * @return  peer.ftp.FtpEntry
     */
    public function current() {
      return $this->e; 
    }
    
    /**
     * Get current key
     *
     * @return  string
     */
    public function key() { 
      return $this->e->getName(); 
    }

    /**
     * Forward to next element
     *
     */    
    public function next() {
      // Intentionally empty, cursor is forwaded inside valid()
    }

    /**
     * Rewind iterator
     *
     */
    public function rewind() { 
      $this->i= 0; 
    }

    /**
     * Check for validity
     *
     * @return  bool
     */
    public function valid() { 
      static $dotdirs= array('.', '..');

      do {
        if ($this->i >= sizeof($this->v)) return FALSE;

        $this->e= $this->c->parser->entryFrom($this->v[$this->i], $this->c);
        $this->i++; 
      } while (in_array($this->e->getName(), $dotdirs));

      return TRUE; 
    }
  }
?>
