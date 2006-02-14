<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  uses('net.xp_framework.beans.entities.FeedValue');

  /**
   * Feed remote home interface
   *
   * @purpose  Demo class  
   */
  class FeedHome extends Interface {

    /**
     * Create method
     *
     * @access  public
     * @param   &net.xp_framework.beans.entities.FeedValue data
     * @return  &net.xp_framework.beans.entities.Feed
     */  
    function &create(&$data) { }
  
    /**
     * Finder method
     *
     * @access  public
     * @param   &wrapper.Long primaryKey
     * @return  &net.xp_framework.beans.entities.Feed
     */
    function &findByPrimaryKey(&$primaryKey) { }

    /**
     * Finder method
     *
     * @access  public
     * @return  &net.xp_framework.beans.entities.Feed[]
     */
    function &findAll() { }
  
  }
?>
