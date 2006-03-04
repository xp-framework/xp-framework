<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoSize'
  );

  /**
   * Container with all sizes of a picture.
   *
   * @see      xp://com.flickr.FlickrPhotoSize
   * @purpose  Container
   */
  class FlickrPhotoSizes extends Object {
    var
      $sizes  = NULL;
    
    /**
     * Constructor
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->sizes= &Collection::forClass('com.flickr.FlickrPhotoSize');
    }
    
    /**
     * Set Client
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    function setClient(&$client) {
      for ($i= 0; $i < $this->sizes->size(); $i++) {
        $s= &$this->sizes->get($i);
        $s->setClient($client);
      }
    }
      
    /**
     * Add new size
     *
     * @access  public
     * @param   &lang.Object size
     */
    #[@xmlmapping(element= 'size', class= 'com.flickr.FlickrPhotoSize')]
    function addSize(&$size) {
      $this->sizes->add($size);
      return $size;
    }
  }
?>
