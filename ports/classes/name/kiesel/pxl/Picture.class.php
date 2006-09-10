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
    var
      $name     = '',
      $date     = NULL,
      $author   = '',
      $filename = NULL,
      $storage  = NULL;
    
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
    function wakeup($context) {
      $this->setStorage($context['storage']);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &create(&$storage) {
      $data= $storage->load('picture');
      if (!$data) return NULL;
      
      $c= &Unmarshaller::unmarshal($data, 'name.kiesel.pxl.Picture');
      $c->setStorage($storage);
      return $c;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    #[@xmlmapping(element= 'name')]
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    #[@xmlmapping(element= 'name')]
    function getName() {
      return $this->name;
    }

    /**
     * Set Date
     *
     * @access  public
     * @param   &lang.Object date
     */
    #[@xmlmapping(element= 'date')]
    function setDate(&$date) {
      $this->date= &Date::fromString($date);
    }

    /**
     * Get Date
     *
     * @access  public
     * @return  &lang.Object
     */
    #[@xmlfactory(element= 'date')]
    function &serializeDate() {
      if (!$this->date) return NULL;
      return $this->date->toString('Y-m-d H:i:s');
    }

    /**
     * Set Author
     *
     * @access  public
     * @param   string author
     */
    #[@xmlmapping(element= 'author')]
    function setAuthor($author) {
      $this->author= $author;
    }

    /**
     * Get Author
     *
     * @access  public
     * @return  string
     */
    #[@xmlfactory(element= 'author')]
    function getAuthor() {
      return $this->author;
    }

    /**
     * Set Filename
     *
     * @access  public
     * @param   &lang.Object filename
     */
    #[@xmlmapping(element= 'filename')]
    function setFilename(&$filename) {
      $this->filename= &$filename;
    }

    /**
     * Get Filename
     *
     * @access  public
     * @return  &lang.Object
     */
    #[@xmlfactory(element= 'filename')]
    function &getFilename() {
      return $this->filename;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function toXml() {
      $filename= $this->storage->getBase().'/'.$this->getFilename();
      try(); {
        $exif= &ExifData::fromFile(new File($filename));
      } if (catch('ImagingException', $e)) {
        $exif= NULL;
      }
    
      $dimensions= getimagesize($filename);
      $n= &new Node('picture', NULL, array(
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
