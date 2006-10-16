<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * IO Element
   *
   * @purpose  Interface
   */
  class IOElement extends Interface {

    /**
     * Retrieve this element's URI
     *
     * @access  public
     * @return  string
     */
    function getURI() { }
    
    /**
     * Retrieve this element's size in bytes
     *
     * @access  public
     * @return  int
     */
    function getSize() { }

    /**
     * Retrieve this element's created date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &createdAt() { }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastAccessed() { }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @access  public
     * @return  &util.Date
     */
    function &lastModified() { }

  }
?>
