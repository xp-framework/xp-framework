<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class CatalogueEntry extends Object {
    public
      $id   = 0,
      $name = '',
      $path = '';

    /**
     * Set Id
     *
     * @param   int id
     */
    #[@xmlmapping(element= '@id')]
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @return  int
     */
    #[@xmlfactory(element= '@id')]
    public function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    #[@xmlmapping(element= '@name')]
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    #[@xmlfactory(element= '@name')]
    public function getName() {
      return $this->name;
    }

    /**
     * Set Path
     *
     * @param   string path
     */
    #[@xmlmapping(element= '@path')]
    public function setPath($path) {
      $this->path= $path;
    }

    /**
     * Get Path
     *
     * @return  string
     */
    #[@xmlfactory(element= '@path')]
    public function getPath() {
      return $this->path;
    }
  }
?>
