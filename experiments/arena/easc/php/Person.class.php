<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Person object
   *
   * @see      xp://SerializerTest
   * @purpose  Helper class for SerializerTest
   */
  class Person extends Object {
    var
      $id     = 1549,
      $name   = 'Timm Friebe';

    /**
     * Set Id
     *
     * @access  public
     * @param   mixed id
     */
    function setId($id) {
      $this->id= $id;
    }

    /**
     * Get Id
     *
     * @access  public
     * @return  mixed
     */
    function getId() {
      return $this->id;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   mixed name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  mixed
     */
    function getName() {
      return $this->name;
    }
  }
?>
