<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.IOException');
   
  /**
   * Shared memory segment
   *
   * @purpose   Provide a wrapper around shared memory segments
   * @ext       sem
   */
  class ShmSegment extends Object {
    var 
      $name     = '',
      $spot     = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->setName($name);
      parent::__construct();
    }
    
    /**
     * Set name
     *
     * @access  public
     * @param   string str
     * @return  string
     */
    function setName($name) {
      $this->name= $name;
      $str= str_pad($name, 4, 'Z');
      $this->spot= '';
      for ($i= 0; $i < 4; $i++) {
        $this->spot.= dechex(ord($str{$i}));
      }
    }
    
    /**
     * Private helper function
     *
     * @access  private
     * @return  mixed data
     */
    function &_get() {
      $h= shm_attach($this->spot);
      $data= shm_get_var($h, $this->name);
      shm_detach($h);
      
      return is_array($data) ? $data : FALSE;
    }
    
    /**
     * Returns whether this segment is empty (i.e., has not been written or was
     * previously removed)
     *
     * @access  public
     * @return  bool TRUE if this segment is empty
     */
    function isEmpty() {
      return (FALSE === $this->_get());
    }
    
    /**
     * Get this segment's contents
     *
     * @access  public
     * @return  &mixed data
     * @throws  IOException in case an error occurs
      */
    function &get() {
      if (FALSE === ($data= $this->_get())) {
        return throw(new IOException('Could not read segment '.$this->name));
      }
      
      return $data[0];
    }

    /**
     * Put this segment's contents
     *
     * @access  public
     * @param   &mixed data
     * @param   int permissions default 0666 permissions
     * @throws  IOException in case an error occurs
     */
    function put(&$val, $permissions= 0666) {
      $v= array($val);
      $h= shm_attach($this->spot, strlen(serialize($v)), $permissions);
      $ret= shm_put_var($h, $this->name, $v);
      shm_detach($h);
      if (FALSE === $ret) {
        return throw(new IOException('Could not write segment '.$this->name));
      }
      
      return $ret;
    }
    
    /**
     * Get this segment's contents
     *
     * @access  public
     * @return  &mixed data
     * @throws  IOException in case an error occurs
     */
    function &remove() {
      $h= shm_attach($this->spot);
      $ret= shm_remove_var($h, $this->name);
      shm_detach($h);
      if (FALSE === $ret) {
        return throw(new IOException('Could not remove segment '.$this->name));
      }
      
      return $ret;
    }

  }
?>
