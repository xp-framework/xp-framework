<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.criterion.Restrictions');

  /**
   * Factory for criterion types
   *
   * @purpose  Factory
   */
  class Property extends Object {
    var 
      $name= '';

    /**
     * Constructor
     *
     * @access  protected
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
    }

    /**
     * Retrieve a property instance by name
     *
     * @model   static
     * @access  public
     * @param   string name
     * @return  &rdbms.criterion.Property
     */
    function &forName($name) {
      static $instances= array();
      
      if (!isset($instances[$name])) {
        $instances[$name]= &new Property($name);
      }
      return $instances[$name];
    }

    /**
     * Apply an "in" constraint to this property
     *
     * @access  public
     * @param   mixed[] values
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &in($values) {
      return Restrictions::in($this->name, $values);
    }

    /**
     * Apply an "not in" constraint to this property
     *
     * @access  public
     * @param   mixed[] values
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &notIn($values) {
      return Restrictions::notIn($this->name, $values);
    }

    /**
     * Apply a "like" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &like($value) {
      return Restrictions::like($this->name, $value);
    }

    /**
     * Apply a case-insensitive "like" constraint to this property
     *
     * @see     php://sql_regcase
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &ilike($value) {
      return Restrictions::ilike($this->name, $value);
    }
        
    /**
     * Apply an "equal" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &equal($value) {
      return Restrictions::equal($this->name, $value);
    }

    /**
     * Apply a "not equal" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &notEqual($value) {
      return Restrictions::notEqual($this->name, $value);
    }

    /**
     * Apply a "less than" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &lessThan($value) {
      return Restrictions::lessThan($this->name, $value);
    }

    /**
     * Apply a "greater than" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &greaterThan($value) {
      return Restrictions::greaterThan($this->name, $value);
    }

    /**
     * Apply a "less than or equal to" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &lessThanOrEqualTo($value) {
      return Restrictions::lessThanOrEqualTo($this->name, $value);
    }

    /**
     * Apply a "greater than or equal to" constraint to this property
     *
     * @access  public
     * @param   mixed value
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &greaterThanOrEqualTo($value) {
      return Restrictions::greaterThanOrEqualTo($this->name, $value);
    }

    /**
     * Apply a "between" constraint to this property
     *
     * @access  public
     * @param   mixed lo
     * @param   mixed hi
     * @return  &rdbms.criterion.SimpleExpression
     */
    function &between($lo, $hi) {
      return Restrictions::between($this->name, $lo, $hi);
    }
  }
?>
