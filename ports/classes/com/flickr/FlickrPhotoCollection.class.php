<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'com.flickr.FlickrPhoto'
  );

  /**
   * Collection of Flickr photos
   *
   * @purpose  Collection of photos
   */
  class FlickrPhotoCollection extends Object {
    public
      $photos   = NULL,
      $page     = 1,
      $pages    = 1,
      $perPage  = 1,
      $total    = 1;
    
    public
      $_client   = NULL;

    /**
     * Set Client
     *
     * @access  public
     * @param   &com.flickr.xmlrpc.FlickrClient client
     */
    public function setClient(&$client) {
      $this->_client= &$client;
      for ($i= 0; $i < $this->photos->size(); $i++) {
        $p= &$this->photos->get($i);
        $p->setClient($client);
      }
    }

    /**
     * Constructor
     *
     * @access  public
     */
    public function __construct() {
      $this->photos= &Collection::forClass('com.flickr.FlickrPhoto');
    }
    
    /**
     * Set Photos
     *
     * @access  public
     * @param   &lang.Object photos
     */
    #[@xmlmapping(element= 'photo', class= 'com.flickr.FlickrPhoto')]
    public function addPhoto(&$photo) {
      $this->photos->add($photo);
      return $photo;
    }

    /**
     * Get Photos
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getPhotos() {
      return $this->photos;
    }

    /**
     * Set Page
     *
     * @access  public
     * @param   mixed page
     */
    #[@xmlmapping(element= '@page')]
    public function setPage($page) {
      $this->page= $page;
    }

    /**
     * Get Page
     *
     * @access  public
     * @return  mixed
     */
    public function getPage() {
      return $this->page;
    }

    /**
     * Set Pages
     *
     * @access  public
     * @param   mixed pages
     */
    #[@xmlmapping(element= '@pages')]
    public function setPages($pages) {
      $this->pages= $pages;
    }

    /**
     * Get Pages
     *
     * @access  public
     * @return  mixed
     */
    public function getPages() {
      return $this->pages;
    }

    /**
     * Set PerPage
     *
     * @access  public
     * @param   mixed perPage
     */
    #[@xmlmapping(element= '@perpage')]
    public function setPerPage($perPage) {
      $this->perPage= $perPage;
    }

    /**
     * Get PerPage
     *
     * @access  public
     * @return  mixed
     */
    public function getPerPage() {
      return $this->perPage;
    }

    /**
     * Set Total
     *
     * @access  public
     * @param   mixed total
     */
    #[@xmlmapping(element= '@total')]
    public function setTotal($total) {
      $this->total= $total;
    }

    /**
     * Get Total
     *
     * @access  public
     * @return  mixed
     */
    public function getTotal() {
      return $this->total;
    }
  }
?>
