<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBTableAttribute', 'rdbms.DBIndex');

  /** 
   * Represents a database table
   *
   */  
  class DBTable extends Object {
    var 
      $name=        '',
      $attributes=  array(),
      $indexes=     array(),
      $constraints= array();

    /**
     * Constructor
     *
     * @access  public
     * @param   string name table's name
     */
    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }

    /**
     * Get a table by it's name
     *
     * @access  static
     * @param   &rdbms.DBAdapter and adapter
     * @param   string name
     * @return  &rdbms.DBTable a table object
     */
    function &getByName(&$adapter, $name) {
      return $adapter->getTable($name);
    }

    /**
     * Get tables by database
     *
     * @access  static
     * @param   &rdbms.DBAdapter and adapter
     * @param   string database
     * @return  &rdbms.DBTable[] an array of table objects
     */
    function &getByDatabase(&$adapter, $database) {
      return $adapter->getTables($database);
    }

    /**
     * Get first attribute - Iterator function
     *
     * @access  public
     * @return  &rdbms.DBAttribute an attribute
     * @see     getNextAttribute
     */
    function &getFirstAttribute() {
      reset($this->attributes);
      return current($this->attributes);
    }

    /**
     * Get next attribute - Iterator function
     *
     * Example:
     * <code>
     *   $table= &DBTable::getByName($adapter, 'person');
     *   $attr= $table->getFirstAttribute();
     *   do {
     *     var_dump($attr);
     *   } while ($attr= $table->getNextAttribute());
     * </code>
     *
     * @access  public
     * @return  &rdbms.DBAttribute an attribute or FALSE if none more exist
     */
    function &getNextAttribute() {
      return next($this->attributes);
    }

    /**
     * Add an attribute
     *
     * @access  public
     * @param   &rdbms.DBAttribute attr the attribute to add
     * @return  &rdbms.DBAttribute the added attribute
     */
    function &addAttribute(&$attr) {
      $this->attributes[]= &$attr;
      return $attr;
    }

    /**
     * Add an index
     *
     * @access  public
     * @param   &rdbms.DBIndex index the index to add
     * @return  &rdbms.DBIndex the added index
     */
    function &addIndex(&$index) {
      $this->indexes[]= &$index;
      return $index;
    }

    /**
     * Get first index - Iterator function
     *
     * @access  public
     * @return  &rdbms.DBIndex an index
     * @see     getNextIndex
     */
    function &getFirstIndex() {
      reset($this->indexes);
      return current($this->indexes);
    }

    /**
     * Get next index - Iterator function
     *
     * @access  public
     * @return  &rdbms.DBIndex an index or FALSE to indicate there are none left
     * @see     getNextIndex
     */
    function &getNextIndex() {
      return next($this->indexes);
    }

    /**
     * Check to see if there is an attribute of this table with the name specified
     *
     * @access  public
     * @param   string name the attribute's name to search for
     * @return  bool TRUE if this attribute exists
     */
    function hasAttribute($name) {
      for ($i= 0, $m= sizeof($this->attributes); $i < $m; $i++) {
        if ($name == $this->attributes[$i]->name) {
          return TRUE;
        }
      }
      return FALSE;
    }
  }
?>
