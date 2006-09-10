<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.Collection', 'xml.PCData', 'xml.Node');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Page extends Object {
    var
      $title        = '',
      $description  = '',
      $pictures     = NULL,
      $comments     = NULL,
      $trackbacks   = NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->pictures= &Collection::forClass('name.kiesel.pxl.Picture');
    }


    /**
     * Get Description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }

    /**
     * Get Comments
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getComments() {
      return $this->comments;
    }

    /**
     * Get Trackbacks
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getTrackbacks() {
      return $this->trackbacks;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &create(&$storage) {
      $data= $storage->load('page');
      if (!$data) return NULL;
      
      $page= &Unmarshaller::unmarshal($data, 'name.kiesel.pxl.Page', array(
        'storage' => &$storage
      ));
      $page && $page->setStorage($storage);
      return $page;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function hibernate() {
      return $this->storage->save('page', Marshaller::marshal($this));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setStorage(&$storage) {
      $this->storage= &$storage;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'description')]
    function setDescription($description) {
      $this->description= trim($description);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'comment')]
    function addComment($comment) {
      $this->comments[]= $comment;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'trackback')]
    function addTrackback($trackback) {
      $this->trackbacks[]= $trackback;
    }
    
    /**
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    #[@xmlmapping(element= 'title')]
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    #[@xmlfactory(element= 'title')]
    function getTitle() {
      return $this->title;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'picture', class= 'name.kiesel.pxl.Picture')]
    function addPicture(&$p) {
      $this->pictures->add($p);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlfactory(element= 'picture')]
    function getPictures() {
      return $this->pictures;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function toXml() {
      $n= &new Node('page');
      $n->addChild(new Node('description', new PCData($this->description)));
      
      for ($i= 0; $i < $this->pictures->size(); $i++) {
        $p= &$this->pictures->get($i);
        $n->addChild($p->toXml());
      }
      
      return $n;
    }    
  }
?>
