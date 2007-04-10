<?php
/* This class is part of the XP framework
 *
 * $Id: DBTable.class.php 9170 2007-01-08 11:39:17Z friebe $
 */

  uses(
    'rdbms.DBTableAttribute',
    'rdbms.DBIndex',
    'rdbms.DBForeignKeyConstraint'
  );

  /** 
   * Represents a database table
   *
   */  
  class DBTable extends Object {
    public 
      $name=          '',
      $attributes=    array(),
      $indexes=       array(),
      $fgConstraints= array();

    /**
     * Constructor
     *
     * @param   string name table's name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Get a table by it's name
     *
     * @param   rdbms.DBAdapter an adapter
     * @param   string name
     * @param   string database default NULL if omitted, uses current database
     * @return  rdbms.DBTable a table object
     */
    public static function getByName($adapter, $name, $database= NULL) {
      return $adapter->getTable($name, $database);
    }

    /**
     * Get tables by database
     *
     * @param   rdbms.DBAdapter and adapter
     * @param   string database
     * @return  rdbms.DBTable[] an array of table objects
     */
    public static function getByDatabase($adapter, $database) {
      return $adapter->getTables($database);
    }

    /**
     * Get first attribute - Iterator function
     *
     * @return  rdbms.DBAttribute an attribute
     * @see     getNextAttribute
     */
    public function getFirstAttribute() {
      reset($this->attributes);
      return current($this->attributes);
    }

    /**
     * Get next attribute - Iterator function
     *
     * Example:
     * <code>
     *   $attr= DBTable::getByName($adapter, 'person')->getFirstAttribute();
     *   do {
     *     var_dump($attr);
     *   } while ($attr= $table->getNextAttribute());
     * </code>
     *
     * @return  rdbms.DBAttribute an attribute or FALSE if none more exist
     */
    public function getNextAttribute() {
      return next($this->attributes);
    }

    /**
     * Add an attribute
     *
     * @param   rdbms.DBAttribute attr the attribute to add
     * @return  rdbms.DBAttribute the added attribute
     */
    public function addAttribute($attr) {
      $this->attributes[]= $attr;
      return $attr;
    }

    /**
     * Add an index
     *
     * @param   rdbms.DBIndex index the index to add
     * @return  rdbms.DBIndex the added index
     */
    public function addIndex($index) {
      $this->indexes[]= $index;
      return $index;
    }

    /**
     * Get first index - Iterator function
     *
     * @return  rdbms.DBIndex an index
     */
    public function getFirstIndex() {
      reset($this->indexes);
      return current($this->indexes);
    }

    /**
     * Get next index - Iterator function
     *
     * @return  rdbms.DBIndex an index or FALSE to indicate there are none left
     */
    public function getNextIndex() {
      return next($this->indexes);
    }

    /**
     * Check to see if there is an attribute of this table with the name specified
     *
     * @param   string name the attribute's name to search for
     * @return  bool TRUE if this attribute exists
     */
    public function hasAttribute($name) {
      for ($i= 0, $m= sizeof($this->attributes); $i < $m; $i++) {
        if ($name == $this->attributes[$i]->name) return TRUE;
      }
      return FALSE;
    }

    /**
     * Add a constraint
     *
     * @param   rdbms.DBForeignKeyConstraint constraint
     * @return  rdbms.DBForeignKeyConstraint the added constraint
     */
    public function addForeignKeyConstraint($constraint) {
      $this->fgConstraints[]= $constraint;
      return $constraint;
    }

    /**
     * Get first constraint - Iterator function
     *
     * @return  rdbms.DBForeignKeyConstraint a constraint
     */
    public function getFirstForeignKeyConstraint() {
      reset($this->fgConstraints);
      return current($this->fgConstraints);
    }

    /**
     * Get next constraint - Iterator function
     *
     * @return  rdbms.DBForeignKeyConstraint a constraint or FALSE to indicate there are none left
     */
    public function getNextForeignKeyConstraint() {
      return next($this->fgConstraints);
    }
  }
?>
