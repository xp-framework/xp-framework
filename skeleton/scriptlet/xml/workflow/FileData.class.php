<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.File');

  /**
   * Represents an uploaded file
   *
   * @purpose  Wrapper
   */
  class FileData extends Object {
    var
      $name = '',
      $type = '',
      $size = 0,
      $file = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name, $type, $size, $uri) {
      $this->name= $name;
      $this->type= $type;
      $this->size= $size;
      $this->file= &new File($uri);
    }

    /**
     * Set File
     *
     * @access  public
     * @param   &io.File file
     */
    function setFile(&$file) {
      $this->file= &$file;
    }

    /**
     * Get File
     *
     * @access  public
     * @return  &io.File
     */
    function &getFile() {
      return $this->file;
    }

      

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
     * Set Type
     *
     * @access  public
     * @param   string type
     */
    function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  string
     */
    function getType() {
      return $this->type;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      return $this->size;
    }

  }
?>
