<?php
/* This class is part of the XP framework
 *
 * $Id: AlbumChapter.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  /**
   * Represents a single chapter within an album.
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class AlbumChapter extends lang::Object {
    public
      $name   = '',
      $images = array();

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Set name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Add an element to images
     *
     * @param   &de.thekid.dialog.AlbumImage image
     */
    public function addImage($image) {
      $this->images[]= $image;
    }

    /**
     * Get one image element by position. Returns NULL if the element 
     * can not be found.
     *
     * @param   int i
     * @return  &de.thekid.dialog.AlbumImage
     */
    public function imageAt($i) {
      if (!isset($this->images[$i])) return NULL;
      return $this->images[$i];
    }

    /**
     * Get number of images
     *
     * @return  int
     */
    public function numImages() {
      return sizeof($this->images);
    }
    
    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      $is= '';
      for ($i= 0, $s= sizeof($this->images); $i < $s; $i++) {
        $is.= '    '.str_replace("\n", "\n  ", $this->images[$i]->toString())."\n";
      }
      return sprintf(
        "%s(%s) {\n%s  }",
        $this->getClassName(),
        $this->name,
        $is
      );
    }
  }
?>
