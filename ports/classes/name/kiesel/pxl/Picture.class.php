<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'img.util.ExifData');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Picture extends Object {
    public
      $name     = '',
      $date     = NULL,
      $author   = '',
      $filename = NULL,
      $storage  = NULL;
    
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
    public function wakeup($context) {
      $this->setStorage($context['storage']);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function create($storage) {
      $data= $storage->load('picture');
      if (!$data) return NULL;
      
      $c= Unmarshaller::unmarshal($data, 'name.kiesel.pxl.Picture');
      $c->setStorage($storage);
      return $c;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    #[@xmlmapping(element= 'name')]
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    #[@xmlmapping(element= 'name')]
    public function getName() {
      return $this->name;
    }

    /**
     * Set Date
     *
     * @param   &lang.Object date
     */
    #[@xmlmapping(element= 'date')]
    public function setDate($date) {
      $this->date= Date::fromString($date);
    }

    /**
     * Get Date
     *
     * @return  &lang.Object
     */
    #[@xmlfactory(element= 'date')]
    public function serializeDate() {
      if (!$this->date) return NULL;
      return $this->date->toString('Y-m-d H:i:s');
    }

    /**
     * Set Author
     *
     * @param   string author
     */
    #[@xmlmapping(element= 'author')]
    public function setAuthor($author) {
      $this->author= $author;
    }

    /**
     * Get Author
     *
     * @return  string
     */
    #[@xmlfactory(element= 'author')]
    public function getAuthor() {
      return $this->author;
    }

    /**
     * Set Filename
     *
     * @param   &lang.Object filename
     */
    #[@xmlmapping(element= 'filename')]
    public function setFilename($filename) {
      $this->filename= $filename;
    }

    /**
     * Get Filename
     *
     * @return  &lang.Object
     */
    #[@xmlfactory(element= 'filename')]
    public function getFilename() {
      return $this->filename;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toXml() {
      $filename= $this->storage->getBase().'/'.$this->getFilename();
      try {
        $exif= ExifData::fromFile(new File($filename));
      } catch (ImagingException $e) {
        $exif= NULL;
      }
    
      $dimensions= getimagesize($filename);
      $n= new Node('picture', NULL, array(
        'author'  => $this->getAuthor(),
        'filename'  => $this->getFilename(),
        'name'      => $this->getName(),
        'width'     => $dimensions[0],
        'height'    => $dimensions[1]
      ));
      $this->date && $n->addChild(Node::fromObject($this->date, 'date'));
      $exif && $n->addChild(Node::fromObject($exif, 'exif'));
      
      return $n;
    }
  }
?>
