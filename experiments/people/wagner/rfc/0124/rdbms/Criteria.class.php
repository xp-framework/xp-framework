<?php
/* This class is part of the XP framework
 *
 * $Id: Criteria.class.php 9315 2007-01-17 15:02:32Z friebe $ 
 */

  uses(
    'rdbms.criterion.SimpleExpression',
    'rdbms.criterion.Projections',
    'rdbms.SQLExpression'
  );
  
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
      $groupings    = array(),
      $projection   = NULL;

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
     * @param   rdbms.criterion.Criterion condition default NULL
     */
    public function __construct($criterion= NULL) {
      if (is('rdbms.criterion.Criterion', $criterion)) {
        $this->conditions[]= $criterion;
      } else if (is_array($criterion)) {
        $this->conditions[]= new SimpleExpression($criterion[0], $criterion[1], $criterion[2]);
        for ($i= 1, $n= func_num_args(); $i < $n; $i++) {
          $criterion= func_get_arg($i);
          $this->conditions[]= new SimpleExpression($criterion[0], $criterion[1], $criterion[2]);
        }
      }
    }
    
    /**
     * Creates a new instance.
     *
     * Fluent interface:
     * <code>
     *   $c= Criteria::newInstance()
     *     ->add('bz_id', 500, EQUAL)
     *     ->add('author', array(1549, 1552), IN)
     *     ->addOrderBy('created_at', DESCENDING)
     *   ;
     * </code>
     *
     * @param   rdbms.Criteria
     */
    public static function newInstance() {
      return new self();
    }
    
    /**
     * Add a condition
     *
     * Example:
     * <code>
     *   with ($c= new Criteria()); {
     *     $c->add(Restrictions::equal('bz_id', 500));
     *     $c->add(Restrictions::in('author', array(1549, 1552)));
     *   }
     * </code>
     *
     * Alternative API example:
     * <code>
     *   with ($c= new Criteria()); {
     *     $c->add('bz_id', 500, EQUAL);
     *     $c->add('author', array(1549, 1552), IN);
     *   }
     * </code>
     *
     * @param   rdbms.criterion.Criterion criterion
     * @return  rdbms.Criteria this object
     */
    public function add($criterion, $value= NULL, $comparison= EQUAL) {
      if (is('rdbms.criterion.Criterion', $criterion)) {
        $this->conditions[]= $criterion;
      } else {
        $this->conditions[]= new SimpleExpression($criterion, $value, $comparison);        
      }
      return $this;
    }

    /**
     * Add order by
     *
     * <code>
     *   with ($c= new Criteria()); {
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
     * @param   string column
     * @param   string order default ASCENDING
     * @return  rdbms.Criteria this object
     */
    public function addOrderBy($column, $order= ASCENDING) {
      $this->orderings[]= array($column, $order);
      return $this;
    }

    /**
     * Add group by
     *
     * @param   string column
     * @return  rdbms.Criteria this object
     */
    public function addGroupBy($column) {
      $this->groupings[]= $column;
      return $this;
    }
    
    /**
     * Set projection
     *
     * @param   rdbms.util.Projection projection
     * @return  rdbms.Criteria this object
     */
    public function setProjection(Projection $projection) {
      $this->projection= $projection;
      return $this;
    }

    /**
     * Set projection for a new clone of this object
     *
     * @param   rdbms.util.Projection projection
     * @return  rdbms.Criteria this object
     */
    public function withProjection(Projection $projection) {
      $crit= clone($this);
      return $crit->setProjection($projection);
    }

    /**
     * Creates a string representation
     *
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
     * @param   &rdbms.DBConnection db
     * @param   array types
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function toSQL($db, $types) {
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
     * get the projection part of a select statement
     *
     * @param   &rdbms.DBConnection db
     * @param   &rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    private function projections($conn, $peer) {
      if (!$this->isProjection()) return array_keys($peer->types);
      return $this->projection->asSql($conn, $peer->types);
    }

    /**
     * test if the expression is a projection
     *
     * @return  bool
     */
    public function isProjection() {
      return (NULL !== $this->projection);
    }

    /**
     * Executes an SQL SELECT statement
     *
     * @param   &rdbms.DBConnection conn
     * @param   &rdbms.Peer peer
     * @return  &rdbms.ResultSet
     */
    public function executeSelect($conn, $peer) {
      return $conn->query(
        'select %c from %c%c', 
        $this->projections($conn, $peer),
        $peer->table,
        $this->toSQL($conn, $peer->types)
      );
    }

  } 
?>
