<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace rdbms;
  uses(
    'rdbms.SQLFragment',
    'rdbms.criterion.Restrictions',
    'rdbms.join.JoinProcessor'
  );

  /**
   * represents a table column
   * should be build via a dataset's factory Dataset::column(name)
   * 
   * <code>
   *   $col= Nmappoint::column('texture_id'); // where Nmappoint is a generated dataset class
   *
   *   $criteria= create(new Criteria())->add(Restrictions::equal($col, 5);
   *   $criteria= create(new Criteria())->add($col->equal(5));
   * </code>
   */
  class Column extends lang::Object implements SQLFragment {
    
    private
      $peer= NULL,
      $type= '',
      $name= '',
      $path= '';

    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     * @param   string name
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($peer, $name) {
      $path= explode(rdbms::join::JoinProcessor::SEPARATOR, $name);
      $this->name= array_pop($path);
      $this->path= $path;
      $this->peer= $peer->getRelatedPeer($path);
      if (!isset($this->peer->types[$this->name])) throw new lang::IllegalArgumentException('field '.$this->name.' does not exist');
      $this->type= $this->peer->types[$this->name][0];
    }

    /**
     * Get type
     *
     * @return  string
     */
    public function getType() {
      return $this->type;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->type.' '.$this->peer->identifier.'.'.$this->name.'>';
    }

    /**
     * Returns the fragment SQL
     *
     * @param   rdbms.DBConnection conn
     * @return  string
     * @throws  rdbms.SQLStateException
     */
    public function asSql(DBConnection $conn) {
      if (rdbms::join::JoinProcessor::isJoinContext()) return rdbms::join::JoinProcessor::pathToKey($this->path).'.'.$this->name;
      return $this->name;
    }

    /**
     * Apply an "in" constraint to this property
     *
     * @param   mixed[] values
     * @return  rdbms.criterion.SimpleExpression
     */
    public function in($values) {
      return rdbms::criterion::Restrictions::in($this, $values);
    }

    /**
     * Apply an "not in" constraint to this property
     *
     * @param   mixed[] values
     * @return  rdbms.criterion.SimpleExpression
     */
    public function notIn($values) {
      return rdbms::criterion::Restrictions::notIn($this, $values);
    }

    /**
     * Apply a "like" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function like($value) {
      return rdbms::criterion::Restrictions::like($this, $value);
    }

    /**
     * Apply a case-insensitive "like" constraint to this property
     *
     * @see     php://sql_regcase
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function ilike($value) {
      return rdbms::criterion::Restrictions::ilike($this, $value);
    }
        
    /**
     * Apply an "equal" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function equal($value) {
      return rdbms::criterion::Restrictions::equal($this, $value);
    }

    /**
     * Apply a "not equal" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function notEqual($value) {
      return rdbms::criterion::Restrictions::notEqual($this, $value);
    }

    /**
     * Apply a "less than" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function lessThan($value) {
      return rdbms::criterion::Restrictions::lessThan($this, $value);
    }

    /**
     * Apply a "greater than" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function greaterThan($value) {
      return rdbms::criterion::Restrictions::greaterThan($this, $value);
    }

    /**
     * Apply a "less than or equal to" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function lessThanOrEqualTo($value) {
      return rdbms::criterion::Restrictions::lessThanOrEqualTo($this, $value);
    }

    /**
     * Apply a "greater than or equal to" constraint to this property
     *
     * @param   mixed value
     * @return  rdbms.criterion.SimpleExpression
     */
    public function greaterThanOrEqualTo($value) {
      return rdbms::criterion::Restrictions::greaterThanOrEqualTo($this, $value);
    }

    /**
     * Apply a "between" constraint to this property
     *
     * @param   mixed lo
     * @param   mixed hi
     * @return  rdbms.criterion.SimpleExpression
     */
    public function between($lo, $hi) {
      return rdbms::criterion::Restrictions::between($this, $lo, $hi);
    }
  }
?>
