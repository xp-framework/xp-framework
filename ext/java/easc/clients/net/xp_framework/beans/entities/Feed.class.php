<?php
/* This file is part of the XP framework's EASC API
 *
 * $Id$
 */

  uses('net.xp_framework.beans.entities.FeedValue');

  /**
   * Feed remote interface
   *
   * @purpose  Demo class  
   */
  interface Feed {

    /**
     * Set Bz_id
     *
     * @param   int bz_id
     */
    public function setBz_id($bz_id);

    /**
     * Get Bz_id
     *
     * @return  int
     */
    public function getBz_id();

    /**
     * Gets value object
     *
     * @return  net.xp_framework.beans.entities.FeedValue
     */  
    public function getFeedValue();
    
    /**
     * Gets feed's id (primary key)
     *
     * @return  lang.types.Long
     */
    public function getFeed_id();

    /**
     * Sets the feed's id
     *
     * @access public
     * @param  lang.types.Long feed_id
     */
    public function setFeed_id($feed_id);

    /**
     * Sets feed's title
     *
     * @return  string
     */
    public function getTitle();

    /**
     * Gets feed's title
     *
     * @access public
     * @param  string title
     */
    public function setTitle($title);

    /**
     * Sets feed's url
     *
     * @return  string
     */
    public function getUrl();

    /**
     * Gets feed's url
     *
     * @access public
     * @param  string url
     */
    public function setUrl($url);
  }
?>
