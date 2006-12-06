<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.IOElement');

  /**
   * Represents a Mock element
   *
   * @see      xp://net.xp_framework.unittest.io.collections.MockCollection
   * @purpose  Mock object
   */
  class MockElement extends Object implements IOElement {
    public
      $uri    = '',
      $size   = 0,
      $adate  = NULL,
      $mdate  = NULL,
      $cdate  = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string uri
     * @param   int size default 0
     * @param   util.Date adate default NULL
     * @param   util.Date adate default NULL
     * @param   util.Date cdate default NULL
     */
    public function __construct($uri, $size= 0, $adate= NULL, $mdate= NULL, $cdate= NULL) {
      $this->uri= $uri;
      $this->size= $size;
      $this->adate= &$adate;
      $this->mdate= &$mdate;
      $this->cdate= &$cdate;
    }

    /**
     * Returns this element's URI
     *
     * @access  public
     * @return  string
     */
    public function getURI() { 
      return $this->uri;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @access  public
     * @return  int
     */
    public function getSize() { 
      return $this->size;
    }

    /**
     * Retrieve this element's created date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &createdAt() {
      return $this->cdate;
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &lastAccessed() {
      return $this->adate;
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @access  public
     * @return  &util.Date
     */
    public function &lastModified() {
      return $this->mdate;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }

  } 
?>
