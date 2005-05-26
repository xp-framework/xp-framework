<?php
/* This class is part of the XP framework's port "Dialog"
 *
 * $Id$ 
 */

  uses('util.Date', 'de.thekid.dialog.AlbumImage', 'de.thekid.dialog.AlbumChapter');

  /**
   * Represents a single album.
   *
   * @purpose  Value object
   */
  class Album extends Object {
    var
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $highlights   = array(),
      $chapters     = array();

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
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
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set CreatedAt
     *
     * @access  public
     * @param   &util.Date createdAt
     */
    function setCreatedAt(&$createdAt) {
      $this->createdAt= &$createdAt;
    }

    /**
     * Get CreatedAt
     *
     * @access  public
     * @return  &util.Date
     */
    function &getCreatedAt() {
      return $this->createdAt;
    }
    
    /**
     * Get date
     *
     * @see     xp://de.thekid.dialog.IEntry
     * @access  public
     * @return  &util.Date
     */
    function &getDate() {
      return $this->createdAt;
    }

    /**
     * Set Description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
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
     * Add an element to highlights
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumImage highlight
     * @return  &de.thekid.dialog.AlbumImage the added highlight
     */
    function &addHighlight(&$highlight) {
      $this->highlights[]= &$highlight;
      return $highlight;
    }

    /**
     * Get one highlight element by position. Returns NULL if the element 
     * can not be found.
     *
     * @access  public
     * @param   int i
     * @return  &de.thekid.dialog.AlbumImage
     */
    function &highlightAt($i) {
      if (!isset($this->highlights[$i])) return NULL;
      return $this->highlights[$i];
    }

    /**
     * Get number of highlights
     *
     * @access  public
     * @return  int
     */
    function numHighlights() {
      return sizeof($this->highlights);
    }

    /**
     * Add an element to chapters
     *
     * @access  public
     * @param   &de.thekid.dialog.AlbumChapter chapter
     * @return  &de.thekid.dialog.AlbumChapter the added chapter
     */
    function &addChapter(&$chapter) {
      $this->chapters[]= &$chapter;
      return $chapter;
    }

    /**
     * Get one chapter element by position. Returns NULL if the element 
     * can not be found.
     *
     * @access  public
     * @param   int i
     * @return  &de.thekid.dialog.AlbumChapter
     */
    function &chapterAt($i) {
      if (!isset($this->chapters[$i])) return NULL;
      return $this->chapters[$i];
    }

    /**
     * Get number of chapters
     *
     * @access  public
     * @return  int
     */
    function numChapters() {
      return sizeof($this->chapters);
    }

    /**
     * Get number of images (highlights excluded)
     *
     * @access  public
     * @return  int
     */
    function numImages() {
      $r= 0;
      for ($i= 0, $s= sizeof($this->chapters); $i < $s; $i++) {
        $r+= $this->chapters[$i]->numImages();
      }
      return $r;
    }
    
    /**
     * Retrieve a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $hs= '';
      for ($i= 0, $s= sizeof($this->highlights); $i < $s; $i++) {
        $hs.= '  '.$this->highlights[$i]->toString()."\n";
      }
      $cs= '';
      for ($i= 0, $s= sizeof($this->chapters); $i < $s; $i++) {
        $cs.= '  '.$this->chapters[$i]->toString()."\n";
      }
      return sprintf(
        "%s(%s)@{\n".
        "  [title        ] %s\n".
        "  [description  ] %s\n".
        "  [createdAt    ] %s\n".
        "  [highlights   ] {\n%s  }\n".
        "  [chapters     ] {\n%s  }\n".
        "}",
        $this->getClassName(),
        $this->name,
        $this->title,
        $this->description,
        xp::stringOf($this->createdAt),
        $hs,
        $cs
      );
    }

  } implements(__FILE__, 'de.thekid.dialog.IEntry');
?>
