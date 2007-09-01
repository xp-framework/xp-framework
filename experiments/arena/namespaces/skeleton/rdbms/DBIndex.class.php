<?php
/* This class is part of the XP framework
 *
 * $Id: DBIndex.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace rdbms;

  /** 
   * Class representing an index
   *
   */
  class DBIndex extends lang::Object {
    public
      $name=     '',
      $keys=     array(),
      $unique=   FALSE,
      $primary=  FALSE;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string[] keys an array of keys this index is composed of
     */
    public function __construct($name, $keys) {
      $this->name= $name;
      $this->keys= $keys;
      
    }

    /**
     * Return whether this is the primary key
     *
     * @return  bool TRUE when this key is the primary key
     */
    public function isPrimaryKey() {
      return $this->primary;
    }

    /**
     * Return whether this index is unique
     *
     * @return  bool TRUE when this is a unique index
     */
    public function isUnique() {
      return $this->unique;
    }

    /**
     * Return this index' name
     *
     * @return  string name
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Returns all keys
     *
     * @return  string[] keys
     */
    public function getKeys() {
      return $this->keys;
    }
  }
?>
