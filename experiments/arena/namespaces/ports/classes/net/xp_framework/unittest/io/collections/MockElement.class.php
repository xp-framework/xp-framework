<?php
/* This class is part of the XP framework
 *
 * $Id: MockElement.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::io::collections;

  ::uses('io.collections.IOElement');

  /**
   * Represents a Mock element
   *
   * @see      xp://net.xp_framework.unittest.io.collections.MockCollection
   * @purpose  Mock object
   */
  class MockElement extends lang::Object implements io::collections::IOElement {
    public
      $uri    = '',
      $size   = 0,
      $adate  = NULL,
      $mdate  = NULL,
      $cdate  = NULL;

    /**
     * Constructor
     *
     * @param   string uri
     * @param   int size default 0
     * @param   util.Date adate default NULL
     * @param   util.Date adate default NULL
     * @param   util.Date cdate default NULL
     */
    public function __construct($uri, $size= 0, $adate= NULL, $mdate= NULL, $cdate= NULL) {
      $this->uri= $uri;
      $this->size= $size;
      $this->adate= $adate;
      $this->mdate= $mdate;
      $this->cdate= $cdate;
    }

    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() { 
      return $this->uri;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return $this->size;
    }

    /**
     * Retrieve this element's created date and time
     *
     * @return  &util.Date
     */
    public function createdAt() {
      return $this->cdate;
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  &util.Date
     */
    public function lastAccessed() {
      return $this->adate;
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  &util.Date
     */
    public function lastModified() {
      return $this->mdate;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }

  } 
?>
