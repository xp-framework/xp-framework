<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('IN',              'in (?)');
  define('NOT_IN',          'not in (?)');
  define('IS',              'is ?');
  define('IS_NOT',          'is not ?');
  define('LIKE',            'like ?');
  define('EQUAL',           '= ?');
  define('NOT_EQUAL',       '!= ?');
  define('LESS_THAN',       '< ?');
  define('GREATER_THAN',    '> ?');
  define('LESS_EQUAL',      '<= ?');
  define('GREATER_EQUAL',   '>= ?');
  define('BIT_AND',         ' & ? = ?');
  
  define('ASCENDING',       'asc');
  define('DESCENDING',      'desc');

  /**
   * Criteria
   *
   * @test     xp://net.xp_framework.unittest.rdbms.CriteriaTest
   * @see      xp://rdbms.DataSet
   * @purpose  purpose
   */
  class Criteria extends Object {
    var 
      $conditions   = array(),
      $orderings    = array(),
      $groupings    = array();
    
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
      static $nullMapping= array(
        EQUAL     => IS,
        NOT_EQUAL => IS_NOT
      );
      
      // Automatically convert '= NULL' to 'is NULL', former is not valid ANSI-SQL
      if (NULL === $value && isset($nullMapping[$comparison]))
        $comparison= $nullMapping[$comparison];
          
      $this->conditions[]= array($key, $value, $comparison);
    }

    /**
     * Add order by
     *
     * <code>
     *   with ($c= &new Criteria()); {
     *     $this->add('bz_id', 500, EQUAL);
     *     $this->addOrderBy('created_at', DESCENDING);
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

    /**
     * Add group by
     *
     * @access  public
     * @param   string column
     */
    function addGroupBy($column) {
      $this->groupings[]= $column;
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= $this->getClassName()."@{\n";
      foreach ($this->conditions as $condition) {
        $s.= sprintf(
          "  [%s %s]\n",
          $condition[0],
          str_replace('?', xp::stringOf($condition[1]), $condition[2])
        );
      }
      return $s.'}';
    }
    
    /**
     * Export SQL
     *
     * @access  public
     * @param   &rdbms.DBConnection db
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    function toSQL(&$db, $types) {
      $sql= '';
      
      // Process conditions
      if (!empty($this->conditions)) {
        $sql.= ' where ';
        foreach ($this->conditions as $condition) {
          if (!isset($types[$condition[0]])) {
            return throw(new SQLStateException('Field "'.$condition[0].'" unknown'));
          }
          
          $sql.= $condition[0].' '.$db->prepare(
            str_replace('?', $types[$condition[0]], $condition[2]).' and ', 
            $condition[1]
          );
        }
        $sql= substr($sql, 0, -4);
      }

      // Process group by
      if (!empty($this->groupings)) {
        $sql= rtrim($sql, ' ').$db->prepare(' group by %c', $this->groupings);
      }

      // Process order by
      if (!empty($this->orderings)) {
        $sql= rtrim($sql, ' ').' order by ';
        foreach ($this->orderings as $order) {
          if (!isset($types[$order[0]])) {
            return throw(new SQLStateException('Field "'.$order[0].'" unknown'));
          }
          $sql.= $order[0].' '.$order[1].', ';
        }
        $sql= substr($sql, 0, -2);
      }
      
      return $sql;
    }
    
    /**
     * Executes an SQL SELECT statement
     *
     * @access  package
     * @param   &rdbms.DBConnection conn
     * @param   &rdbms.Peer peer
     * @return  &rdbms.ResultSet
     */
    function executeSelect(&$conn, &$peer) {
      return $conn->query(
        'select %c from %c%c', 
        array_keys($peer->types),
        $peer->table,
        $this->toSQL($conn, $peer->types)
      );
    }

  } implements(__FILE__, 'rdbms.SQLExpression');
?>
