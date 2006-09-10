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
    var
      $id   = 0,
      $name = '',
      $path = '';

    /**
     * Set Id
     *
     * @access  public
     * @param   int id
     */
    #[@xmlmapping(element= '@id')]
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  int
     */
    #[@xmlfactory(element= '@id')]
    function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    #[@xmlmapping(element= '@name')]
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    #[@xmlfactory(element= '@name')]
    function getName() {
      return $this->name;
    }

    /**
     * Set Path
     *
     * @access  public
     * @param   string path
     */
    #[@xmlmapping(element= '@path')]
    function setPath($path) {
      $this->path= $path;
    }

    /**
     * Get Path
     *
     * @access  public
     * @return  string
     */
    #[@xmlfactory(element= '@path')]
    function getPath() {
      return $this->path;
    }
  }
?>
