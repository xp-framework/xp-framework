<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a Mock element
   *
   * @see      xp://net.xp_framework.unittest.io.collections.MockCollection
   * @purpose  Mock object
   */
  class MockElement extends Object {
    var
      $uri    = '',
      $size   = 0,
      $adate  = NULL,
      $mdate  = NULL,
      $cdate  = NULL;

    /**
     * Constructor
     *
     * @access  publid
     * @param   string uri
     * @param   int size default 0
     * @param   util.Date adate default NULL
     * @param   util.Date adate default NULL
     * @param   util.Date cdate default NULL
     */
    function __construct($uri, $size= 0, $adate= NULL, $mdate= NULL, $cdate= NULL) {
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
    function getURI() { 
      return $this->uri;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @access  public
     * @return  int
     */
    function getSize() { 
      return $this->size;
    }

    /**
     * Retrieve this element's created date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &createdAt() {
      return $this->cdate;
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastAccessed() {
      return $this->adate;
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastModified() {
      return $this->mdate;
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() { 
      return $this->getClassName().'('.$this->uri.')';
    }

  } implements(__FILE__, 'io.collections.IOElement');
?>
