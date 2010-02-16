<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.query.SelectQuery', 'lang.IllegalArgumentException', 'rdbms.Record');

  /**
   * Class for sql set operations union, intercept and except.
   *
   * Use the factory methods
   * <ul>
   *   <li>SetOperation::union(SelectQueryExecutable, SelectQueryExecutable, [ALL])</li>
   *   <li>SetOperation::union(SelectQueryExecutable, SelectQueryExecutable, [ALL])</li>
   *   <li>SetOperation::union(SelectQueryExecutable, SelectQueryExecutable, [ALL])</li>
   * </ul>
   * instead of direct instantiation.
   *
   * This class implements the SelectQueryExecutable interface,
   * so it can be reused as an argument for it's own methods.
   *
   * Example
   * =======
   * <code>
   *   $overaged= new Query(Person::getPeer());
   *   $overaged->addRestriction(
   *     Restrictions::greaterThan(
   *       SQLFuctions::getdate(),
   *       SQLFunctions::dateadd('year', 21, Person::column('b_date'))
   *     )
   *   );
   *   
   *   // query to select all persons who's age are under 16 years
   *   $underaged= new Query(Person::getPeer);
   *   $underaged->addRestriction(
   *     Restrictions::greaterThan(
   *       SQLFunctions::dateadd('year', 16, Person::column('b_date')),
   *       SQLFuctions::getdate()
   *     )
   *   );
   *   
   *   // get all persons from the person table that are not customers yet
   *   // We assume that the Customer table and the Person table have an equal
   *   // column structure. If not you have to add a projection to the criteria.
   *   SetOperation::except(
   *     SetOperation::union($overaged, $underaged), 
   *     new Query(Customer::getPeer())
   *   )->fetchArray();
   * </code>
   *
   * @see      xp://rdbms.query.SelectQuery
   * @see      xp://rdbms.query.Query
   * @purpose  rdbms.query
   */
  class SetOperation extends Object implements SelectQueryExecutable {
    const UNION=         'union';
    const UNION_ALL=     'union_all';
    const INTERCEPT=     'intercept';
    const INTERCEPT_ALL= 'intercept_all';
    const EXCEPT=        'except';
    const EXCEPT_ALL=    'except_all';

    static private
      $sql= array(
        self::UNION         => '%s union %s',
        self::UNION_ALL     => '%s union all %s',
        self::INTERCEPT     => '%s intercept %s',
        self::INTERCEPT_ALL => '%s intercept all %s',
        self::EXCEPT        => '%s except %s',
        self::EXCEPT_ALL    => '%s except all %s',
      );
   
    private
      $arg1 = NULL,
      $arg2 = NULL,
      $mode = NULL;
    
    /**
     * Constructor
     *
     * @param   string mode one of SetOperation::UNION, SetOperation::UNION_ALL, SetOperation::INTERCEPT, SetOperation::INTERCEPT_ALL, SetOperation::EXCEPT or SetOperation::EXCEPT_ALL
     * @param   rdbms.query.SelectQueryExecutable arg1
     * @param   rdbms.query.SelectQueryExecutable arg2
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($mode, SelectQueryExecutable $arg1, SelectQueryExecutable $arg2) {
      $this->mode= $mode;
      $this->arg1= $arg1;
      $this->arg2= $arg2;
    }

    /**
     * get sql query as string
     *
     * @return string
     */
    public function getQueryString() {
      return sprintf(self::$sql[$this->mode], $this->arg1->getQueryString(), $this->arg2->getQueryString());
    }
    
    /**
     * get connection
     *
     * @return rdbms.DBConnection
     */
    public function getConnection() {
      return $this->arg1->getConnection();
    }
    
    /**
     * execute query
     *
     * @param  var[] values
     * @return rdbms.ResultSet
     * @throws lang.IllegalStateException
     */
    public function execute($values= NULL) {
      return $this->arg1->getConnection()->query($this->getQueryString());
    }
    
    /**
     * Retrieve a number of objects from the database
     *
     * @param   int max default 0
     * @return  rdbms.Record[]
     * @throws  lang.IllegalStateException
     */
    public function fetchArray($max= 0) {
      $q= $this->execute();
      for ($i= 1; $record= $q->next(); $i++) {
        if ($max && $i > $max) break;
        $r[]= new Record($record);
      }
      return $r;
    }

    /**
     * Returns an iterator for the select statement
     *
     * @return  rdbms.ResultIterator
     * @see     xp://lang.XPIterator
     * @throws  lang.IllegalStateException
     */
    public function fetchIterator() {
      return new ResultIterator($this->execute(), 'Record');
    }

    /**
     * factory for a union set operation
     *
     * @param   rdbms.query.SelectQueryExecutable arg1
     * @param   rdbms.query.SelectQueryExecutable arg2
     * @param   boll all true for all defaults to false
     * @return  rdbms.query.SetOperation
     */
    public static function union(SelectQueryExecutable $arg1, SelectQueryExecutable $arg2, $all= false) {
      return new self(($all ? self::UNION_ALL : self::UNION), $arg1, $arg2);
    }
    
    /**
     * factory for an intercept set operation
     *
     * @param   rdbms.query.SelectQueryExecutable arg1
     * @param   rdbms.query.SelectQueryExecutable arg2
     * @param   boll all true for all defaults to false
     * @return  rdbms.query.SetOperation
     */
    public static function intercept(SelectQueryExecutable $arg1, SelectQueryExecutable $arg2, $all= false) {
      return new self(($all ? self::INTERCEPT_ALL : self::INTERCEPT), $arg1, $arg2);
    }
    
    /**
     * factory for an except set operation
     *
     * @param   rdbms.query.SelectQueryExecutable arg1
     * @param   rdbms.query.SelectQueryExecutable arg2
     * @param   boll all true for all defaults to false
     * @return  rdbms.query.SetOperation
     */
    public static function except(SelectQueryExecutable $arg1, SelectQueryExecutable $arg2, $all= false) {
      return new self(($all ? self::EXCEPT_ALL : self::EXCEPT), $arg1, $arg2);
    }
  }
?>
