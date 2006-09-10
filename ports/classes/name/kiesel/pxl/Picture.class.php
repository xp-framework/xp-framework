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
    function &getDate() {
      return $this->date;
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
      try(); {
        $exif= &ExifData::fromFile($this->storage->getBase().'/'.$this->getFilename());
      } if (catch('ImagingException', $e)) {
        $exif= NULL;
      }
    
      $n= &new Node('picture', NULL, array(
        'author'  => $this->getAuthor(),
        'filename'  => $this->getFilename(),
        'name'      => $this->getName()
      ));
      $this->date && $n->addChild(Node::fromObject($this->date, 'date'));
      $exif && $n->addChild(Node::fromObject($exif, 'exif'));
      
      return $n;
    }
  }
?>
