<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.criterion.SimpleExpression',
    'rdbms.join.JoinProcessor',
    'rdbms.SQLExpression',
    'rdbms.Column',
    'rdbms.criterion.Projections',
    'rdbms.join.Fetchmode'
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
      $projection   = NULL,
      $fetchmode    = array();

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
     *   $c= create(new Criteria())
     *     ->add('bz_id', 500, EQUAL)
     *     ->add('author', array(1549, 1552), IN)
     *     ->addOrderBy('created_at', DESCENDING)
     *   ;
     * </code>
     *
     * @deprecated Use create() core functionality instead
     * @return  rdbms.Criteria
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
      if ($criterion instanceof Criterion) {
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
     * param can also be a rdbms.Column, a property
     * If the first parameter is omitted or NULL given the projection will be cleared
     * projection is then assumed
     *
     * @param   rdbms.SQLRenderable projection optional
     * @param   string optional alias
     * @return  rdbms.Criteria this object
     */
    public function setProjection(SQLRenderable $projection= NULL, $alias= '') {
      $this->projection= (is_null($projection) || ($projection instanceof ProjectionList))
        ? $projection
        : $projection= Projections::ProjectionList()->add($projection, $alias)
      ;
      return $this;
    }

    /**
     * Set projection for a new clone of this object
     *
     * @param   rdbms.SQLRenderable projection
     * @param   string optional alias
     * @return  rdbms.Criteria this object
     */
    public function withProjection(SQLRenderable $projection, $alias= '') {
      $crit= clone($this);
      return $crit->setProjection($projection, $alias);
    }

    /**
     * set the fetchmode for a path
     *
     * @param   rdbms.join.Fetchmode fetchmode
     * @return  rdbms.Criteria this object
     */
    public function setFetchmode(Fetchmode $fetchmode) {
      $this->fetchmode[$fetchmode->getPath()]= $fetchmode->getMode();
      return $this;
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
     * @param   rdbms.DBConnection db
     * @param   rdbms.Peer peer
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function toSQL(DBConnection $conn, Peer $peer) {
      $sql= '';

      // Process conditions
      if ($this->isJoin()) {
        $sql= (empty($this->conditions) ? '1 = 1' : $this->conditions($conn, $peer));
      } else {
        $sql= (empty($this->conditions) ? '' : ' where '.$this->conditions($conn, $peer));
      }

      // Process group by
      if (!empty($this->groupings)) {
        $sql.= ' group by ';
        foreach ($this->groupings as $grouping) $sql.= $this->fragment($conn, $peer->types, $grouping).', ';
        $sql= substr($sql, 0, -2);
      }

      // Process order by
      if (!empty($this->orderings)) {
        $sql.= ' order by ';
        foreach ($this->orderings as $order) $sql.= $this->fragment($conn, $peer->types, $order[0]).' '.$order[1].', ';
        $sql= substr($sql, 0, -2);
      }

      return $sql;
    }

    /**
     * get conditions as string
     *
     * @param   rdbms.DBConnection db
     * @param   rdbms.Peer peer
     * @return  string
     */
    private function conditions(DBConnection $conn, Peer $peer) {
      $cond= '';
      foreach ($this->conditions as $condition) $cond.= $condition->asSql($conn, $peer).' and ';
      return substr($cond, 0, -5);
    }

    /**
     * get the projection part of a select statement
     *
     * @param   rdbms.DBConnection db
     * @param   rdbms.Peer peer
     * @param   rdbms.join.Joinprocessor jp optional
     * @return  string[]
     * @throws  rdbms.SQLStateException
     */
    public function projections(DBConnection $conn, Peer $peer, $jp= NULL) {
      $result= '';
      if ($this->isProjection()) {
        if ($this->isJoin()) $jp->enterJoinContext();
        $result= $this->projection->asSql($conn);
        if ($this->isJoin()) $jp->leaveJoinContext();
      } else if ($this->isJoin()) {
        $result= $jp->getAttributeString();
      } else {
        $result= array_keys($peer->types);
      }
      return $result;
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
     * test if the expression is a join
     *
     * @return  bool
     */
    public function isJoin() {
      return (0 < sizeof(array_keys($this->fetchmode, 'join')));
    }

    /**
     * Executes an SQL SELECT statement
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @param   rdbms.join.Joinprocessor jp optional
     * @return  rdbms.ResultSet
     */
    public function executeSelect(DBConnection $conn, Peer $peer, $jp= NULL) {
      return $conn->query($this->getSelectQueryString($conn, $peer, $jp));
    }
    
    /**
     * get the SELECT query
     *
     * @param   rdbms.DBConnection conn
     * @param   rdbms.Peer peer
     * @param   rdbms.join.Joinprocessor jp optional
     * @return  rdbms.ResultSet
     */
    public function getSelectQueryString(DBConnection $conn, Peer $peer, $jp= NULL) {
      if ($this->isJoin()) $jp->setFetchmodes($this->fetchmode);
      return $conn->prepare(
        'select %c from %c %c',
        $this->projections($conn, $peer, $jp),
        (($this->isJoin()) ? $jp->getJoinString() : $peer->table),
        $this->toSQL($conn, $peer, $jp)
      );
    }
    
    /**
     * Get a string for a column
     *
     * @param   rdbms.DBConnection conn
     * @param   array types
     * @param   mixed col either an rdbms.Column object or a string containing the column's name
     * @return  string
     */
    private function fragment($conn, $types, $col) {
      if ($col instanceof SQLRenderAble) {
        return $col->asSQL($conn);
      } else {
        if (!isset($types[$col])) throw(new SQLStateException('Field "'.$col.'" unknown'));
        return $col;
      }
    }
  } 
?>
