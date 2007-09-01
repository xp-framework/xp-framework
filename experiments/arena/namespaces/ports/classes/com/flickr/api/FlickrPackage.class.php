<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrPackage.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr::api;

  /**
   * Base class for a flickr packages
   *
   * @see      xp://com.flickr.xmlrpc.FlickrClient
   * @purpose  Flickr package base class
   */
  class FlickrPackage extends lang::Object {
    public
      $client   = NULL;
    
    /**
     * Sets the client for this package
     *
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    public function setClient($client) {
      $this->client= $client;
    }    
  }
?>
