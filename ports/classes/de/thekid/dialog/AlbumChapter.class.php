<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a single chapter within an album.
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class AlbumChapter extends Object {
    var
      $name   = '',
      $images = array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
    }

    /**
     * Set name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Add an element to images
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage image
     */
    function addImage(&$image) {
      $this->images[]= &$image;
    }

    /**
     * Get one image element by position. Returns NULL if the element 
     * can not be found.
     *
     * @access  public
     * @param   int i
     * @return  &de.thekid.dialog.AlbumImage
     */
    function &imageAt($i) {
      if (!isset($this->images[$i])) return NULL;
      return $this->images[$i];
    }

    /**
     * Get number of images
     *
     * @access  public
     * @return  int
     */
    function numImages() {
      return sizeof($this->images);
    }
    
    /**
     * Retrieve a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
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
