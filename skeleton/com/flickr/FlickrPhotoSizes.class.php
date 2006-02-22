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
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FlickrPhotoSizes extends Object {
    var
      $sizes  = NULL;
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->sizes= &Collection::forClass('com.flickr.FlickrPhotoSize');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'size', class= 'com.flickr.FlickrPhotoSize')]
    function addSize(&$size) {
      $this->sizes->add($size);
      return $size;
    }
  }
?>
