<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.criterion.SimpleExpression', 'rdbms.SQLExpression');
  
  define('ASCENDING',       'asc');
  define('DESCENDING',      'desc');

  /**
   * Criteria
   *
   * @test     xp://net.xp_framework.unittest.rdbms.CriteriaTest
   * @see      xp://rdbms.DataSet
   * @purpose  purpose
   */
  class Criteria extends Object implements SQLExpression {
    public 
      $conditions   = array(),
      $orderings    = array(),
      $groupings    = array();
    
    /**
     * Constructor
     *
     * Example:
     * <code>
     *   new Criteria(Restrictions::equal('domainname', 'xp-framework.net'));
     * </code>
     *
     * Alternative API example:
     * <code>
     *   new Criteria(array('domainname', 'xp-framework.net', EQUAL));
     * </code>
     *
     * @access  public
     * @param   rdbms.criterion.Criterion condition default NULL
     */
    public function __construct($criterion= NULL) {
      if (is('rdbms.criterion.Criterion', $criterion)) {
        $this->conditions[]= &$criterion;
      } else if (is_array($criterion)) {
        $this->conditions[]= new SimpleExpression($criterion[0], $criterion[1], $criterion[2]);
        for ($i= 1, $n= func_num_args(); $i < $n; $i++) {
          $criterion= func_get_arg($i);
          $this->conditions[]= new SimpleExpression($criterion[0], $criterion[1], $criterion[2]);
        }
      }
    }
    
    /**
     * Add a condition
     *
     * Example:
     * <code>
     *   with ($c= &new Criteria()); {
     *     $c->add(Restrictions::equal('bz_id', 500));
     *     $c->add(Restrictions::in('author', array(1549, 1552)));
     *   }
     * </code>
     *
     * Alternative API example:
     * <code>
     *   with ($c= &new Criteria()); {
     *     $c->add('bz_id', 500, EQUAL);
     *     $c->add('author', array(1549, 1552), IN);
     *   }
     * </code>
     *
     * @access  public
     * @param   rdbms.criterion.Criterion criterion
     */
    public function add($criterion, $value= NULL, $comparison= EQUAL) {
      if (is('rdbms.criterion.Criterion', $criterion)) {
        $this->conditions[]= &$criterion;
      } else {
        $this->conditions[]= new SimpleExpression($criterion, $value, $comparison);        
      }
    }

    /**
     * Add order by
     *
     * <code>
     *   with ($c= &new Criteria()); {
     *     $c->add(Restriction::equal('bz_id', 500));
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
    public function addOrderBy($column, $order= ASCENDING) {
      $this->orderings[]= array($column, $order);
    }

    /**
     * Add group by
     *
     * @access  public
     * @param   string column
     */
    public function addGroupBy($column) {
      $this->groupings[]= $column;
    }
    
    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName()."@{\n";
      foreach ($this->conditions as $condition) {
        $s.= '  '.xp::stringOf($condition);
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
    public function toSQL(&$db, $types) {
      $sql= '';
      
      // Process conditions
      if (!empty($this->conditions)) {
        $sql.= ' where ';
        foreach ($this->conditions as $condition) {
          $sql.= $condition->asSql($db, $types).' and ';
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
            throw(new SQLStateException('Field "'.$order[0].'" unknown'));
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
    public function executeSelect(&$conn, &$peer) {
      return $conn->query(
        'select %c from %c%c', 
        array_keys($peer->types),
        $peer->table,
        $this->toSQL($conn, $peer->types)
      );
    }

  } 
?>
