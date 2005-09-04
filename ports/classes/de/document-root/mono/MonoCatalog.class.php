<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoCatalog extends Object {
    var
      $creator      = '',
      $name         = '',
      $sequence     = array(),
      $last_id      = 0;

    /**
     * Set Creator
     *
     * @access  public
     * @param   string creator
     */
    function setCreator($creator) {
      $this->creator= $creator;
    }

    /**
     * Get Creator
     *
     * @access  public
     * @return  string
     */
    function getCreator() {
      return $this->creator;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set Sequence
     *
     * @access  public
     * @param   mixed[] sequence
     */
    function setSequence($sequence) {
      $this->sequence= $sequence;
    }

    /**
     * Get Sequence
     *
     * @access  public
     * @return  mixed[]
     */
    function getSequence() {
      return $this->sequence;
    }

    /**
     * Set Last_id
     *
     * @access  public
     * @param   int last_id
     */
    function setLast_id($last_id) {
      $this->last_id= $last_id;
    }

    /**
     * Get Last_id
     *
     * @access  public
     * @return  int
     */
    function getLast_id() {
      return $this->last_id;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function containsId($id) {
      return in_array($id, $this->sequence);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function appendShot($id) {
      if ($this->containsId($id)) {
        return throw(new IllegalStateException(
          'Shot '.$id.' already registered in this album.'
        ));
      }

      $this->sequence[]= $id;
    }
  }
?>
