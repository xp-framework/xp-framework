<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a collection of any IEntry objects
   *
   * @see      xp://de.thekid.dialog.IEntry
   * @purpose  Value object
   */
  class EntryCollection extends Object {
    var
      $name         = '',
      $title        = '',
      $createdAt    = NULL,
      $description  = '',
      $entries      = array();

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
     * Set title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set createdAt
     *
     * @access  public
     * @param   &lang.Object createdAt
     */
    function setCreatedAt(&$createdAt) {
      $this->createdAt= &$createdAt;
    }

    /**
     * Get createdAt
     *
     * @access  public
     * @return  &lang.Object
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
     * Set description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
    }

    /**
     * Add an element to entries
     *
     * @access  public
     * @param   &de.thekid.dialog.IEntry entry
     * @return  &de.thekid.dialog.IEntry the added entry
     */
    function &addEntry(&$entry) {
      $this->entries[]= &$entry;
      return $entry;
    }

    /**
     * Get one entry element by position. Returns NULL if the element 
     * can not be found.
     *
     * @access  public
     * @param   int i
     * @return  &de.thekid.dialog.IEntry
     */
    function &entryAt($i) {
      if (!isset($this->entries[$i])) return NULL;
      return $this->entries[$i];
    }

    /**
     * Get number of entries
     *
     * @access  public
     * @return  int
     */
    function numEntries() {
      return sizeof($this->entries);
    }
  
  } implements(__FILE__, 'de.thekid.dialog.IEntry');
?>
