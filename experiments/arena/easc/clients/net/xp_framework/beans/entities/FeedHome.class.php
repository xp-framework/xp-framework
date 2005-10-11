<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  /**
   * Feed remote home interface
   *
   * @purpose  Demo class  
   */
  class FeedHome extends Interface {
  
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
