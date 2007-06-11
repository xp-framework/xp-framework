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
  interface IOElement {

    /**
     * Retrieve this element's URI
     *
     * @return  string
     */
    public function getURI();
    
    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize();

    /**
     * Retrieve this element's created date and time
     *
     * @return  util.Date
     */
    public function createdAt();

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  util.Date
     */
    public function lastAccessed();

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  util.Date
     */
    public function lastModified();

  }
?>
