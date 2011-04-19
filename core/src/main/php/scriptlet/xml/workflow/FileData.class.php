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
     * @param   io.File file
     */
    public function setFile($file) {
      $this->file= $file;
    }

    /**
     * Get File
     *
     * @return  io.File
     */
    public function getFile() {
      return $this->file;
    }

      

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Type
     *
     * @param   string type
     */
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get Type
     *
     * @return  string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Set Size
     *
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

  }
?>
