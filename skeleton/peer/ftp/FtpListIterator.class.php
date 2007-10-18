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
      $e= NULL,
      $b= '';

    /**
     * Constructor
     *
     * @param   string[] v
     * @param   peer.ftp.FtpConnection c
     * @param   string base default "/"
     */
    public function __construct($v, FtpConnection $c, $base= '/') { 
      $this->v= $v; 
      $this->c= $c; 
      $this->b= $base;
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
      $dotdirs= array($this->b.'./', $this->b.'../');
      do {
        if ($this->i >= sizeof($this->v)) return FALSE;

        $this->e= $this->c->parser->entryFrom($this->v[$this->i], $this->c, $this->b);
        $this->i++; 
      } while (in_array($this->e->getName(), $dotdirs));

      return TRUE; 
    }
  }
?>
