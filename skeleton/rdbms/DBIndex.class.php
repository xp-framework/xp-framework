<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /** 
   * Class representing an index
   *
   */
  class DBIndex extends Object {
    var
      $name=     '',
      $keys=     array(),
      $unique=   FALSE,
      $primary=  FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   string[] keys an array of keys this index is composed of
     */
    function __construct($name, $keys) {
      $this->name= $name;
      $this->keys= $keys;
      
    }

    /**
     * Return whether this is the primary key
     *
     * @access  public
     * @return  bool TRUE when this key is the primary key
     */
    function isPrimaryKey() {
      return $this->primary;
    }

    /**
     * Return whether this index is unique
     *
     * @access  public
     * @return  bool TRUE when this is a unique index
     */
    function isUnique() {
      return $this->unique;
    }

    /**
     * Return this index' name
     *
     * @access  public
     * @return  string name
     */
    function getName() {
      return $this->name;
    }

    /**
     * Returns all keys
     *
     * @access  public
     * @return  string[] keys
     */
    function getKeys() {
      return $this->keys;
    }
  }
?>
