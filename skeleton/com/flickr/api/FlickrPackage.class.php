<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Base class for a flickr packages
   *
   * @see      xp://com.flickr.xmlrpc.FlickrClient
   * @purpose  Flickr package base class
   */
  class FlickrPackage extends Object {
    var
      $client   = NULL;
    
    /**
     * Sets the client for this package
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    function setClient(&$client) {
      $this->client= &$client;
    }    
  }
?>
