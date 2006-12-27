<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Collection',
    'xml.PCData',
    'xml.Node'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Page extends Object {
    public
      $title        = '',
      $description  = '',
      $pictures     = NULL,
      $comments     = NULL,
      $trackbacks   = NULL;

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->pictures= Collection::forClass('name.kiesel.pxl.Picture');
    }


    /**
     * Get Description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }

    /**
     * Get Comments
     *
     * @return  &lang.Object
     */
    public function getComments() {
      return $this->comments;
    }

    /**
     * Get Trackbacks
     *
     * @return  &lang.Object
     */
    public function getTrackbacks() {
      return $this->trackbacks;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function create($storage) {
      $data= $storage->load('page');
      if (!$data) return NULL;
      
      $page= Unmarshaller::unmarshal($data, 'name.kiesel.pxl.Page', array(
        'storage' => $storage
      ));
      $page && $page->setStorage($storage);
      return $page;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function hibernate() {
      return $this->storage->save('page', Marshaller::marshal($this));
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setStorage($storage) {
      $this->storage= $storage;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'description')]
    public function setDescription($description) {
      $this->description= trim($description);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'comment')]
    public function addComment($comment) {
      $this->comments[]= $comment;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'trackback')]
    public function addTrackback($trackback) {
      $this->trackbacks[]= $trackback;
    }
    
    /**
     * Set Title
     *
     * @param   string title
     */
    #[@xmlmapping(element= 'title')]
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @return  string
     */
    #[@xmlfactory(element= 'title')]
    public function getTitle() {
      return $this->title;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'picture', class= 'name.kiesel.pxl.Picture')]
    public function addPicture($p) {
      $this->pictures->add($p);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlfactory(element= 'picture')]
    public function getPictures() {
      return $this->pictures;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toXml() {
      $n= new Node('page');
      $n->addChild(new Node('description', new PCData($this->description)));
      
      for ($i= 0; $i < $this->pictures->size(); $i++) {
        $p= $this->pictures->get($i);
        $n->addChild($p->toXml());
      }
      
      return $n;
    }    
  }
?>
