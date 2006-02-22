<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhotoCollection'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FlickrInterestingnessResponse extends Object {
    var
      $photos   =  NULL;
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->photos= &Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    function addPhoto(&$photo) {
      $this->photos->add($photo);
    }
  }
?>
