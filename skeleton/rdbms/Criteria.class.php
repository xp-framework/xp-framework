<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('IN',              'in (?)');
  define('NOT_IN',          'not in (?)');
  define('LIKE',            'like ?');
  define('EQUAL',           '= ?');
  define('NOT_EQUAL',       '!= ?');
  define('LESS_THAN',       '< ?');
  define('GREATER_THAN',    '> ?');
  define('LESS_EQUAL',      '<= ?');
  define('GREATER_EQUAL',   '>= ?');
  
  define('ASCENDING',       'asc');
  define('DESCENDING',      'desc');

  /**
   * Criteria
   *
   * @see      xp://rdbms.DataSet
   * @purpose  purpose
   */
  class Criteria extends Object {
    var 
      $conditions   = array(),
      $orderings    = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   array* conditions
     */
    function __construct() {
      $this->conditions= func_get_args();
    }
    
    /**
     * Add a condition
     *
     * The order parameter may be one of the following constants:
     * <ul>
     *   <li>IN</li>
     *   <li>NOT_IN</li>
     *   <li>LIKE</li>
     *   <li>EQUAL</li>
     *   <li>NOT_EQUAL</li>
     *   <li>LESS_THAN</li>
     *   <li>GREATER_THAN</li>
     *   <li>LESS_EQUAL</li>
     *   <li>GREATER_EQUAL</li>
     * </ul>
     *
     * @access  public
     * @param   string key
     * @param   mixed value
     * @param   string comparison default EQUAL
     */
    function add($key, $value, $comparison= EQUAL) {
      $this->conditions[]= array($key, $value, $comparison);
    }

    /**
     * Add an order
     *
     * <code>
     *   with ($c= &new Criteria()); {
     *     $c->add('bz_id', 500, EQUAL);
     *     $c->addOrderBy('created_at', DESCENDING);
     *   }
     * </code>
     *
     * The order parameter may be one of the following constants:
     * <ul>
     *   <li>ASCENDING</li>
     *   <li>DESCENDING</li>
     * </ul>
     *
     * @access  public
     * @param   string column
     * @param   string order default ASCENDING
     */
    function addOrderBy($column, $order= ASCENDING) {
      $this->orderings[]= array($column, $order);
    }
  }
?>
