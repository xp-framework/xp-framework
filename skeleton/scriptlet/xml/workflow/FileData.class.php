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
    public
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
    public function __construct($name, $type, $size, $uri) {
      $this->name= $name;
      $this->type= $type;
      $this->size= $size;
      $this->file= new File($uri);
    }

    /**
     * Set File
     *
     * @access  public
     * @param   &io.File file
     */
    public function setFile(&$file) {
      $this->file= &$file;
    }

    /**
     * Get File
     *
     * @access  public
     * @return  &io.File
     */
    public function &getFile() {
      return $this->file;
    }

      

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Type
     *
     * @access  public
     * @param   string type
     */
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

  }
?>
