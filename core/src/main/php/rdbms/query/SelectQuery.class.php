<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'lang.IllegalStateException',
    'rdbms.query.Query',
    'rdbms.query.SelectQueryExecutable'
  );

  /**
   * Store a complete select query
   * This class implements the SelectQueryExecutable interface,
   * so it can be reused by the methods of SetOperation
   *
   * <code>
   *   // query to select all persons who's age are over 21 years
   *   $overaged= new Query(Person::getPeer);
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
   *   // get overaged persons
   *   $overaged->fetchArray();
   *   
   *   // get underaged persons
   *   $underaged->fetchArray();
   *   
   *   // get both
   *   SetOperation::union($overaged, $underaged)->fetchArray();
   * </code>
   *
   * @see      xp://rdbms.query.SetOperation
   * @see      xp://rdbms.query.Query
   * @purpose  SelectQueryExecutable implementation
   */
  class SelectQuery extends Query implements SelectQueryExecutable {

    /**
     * get sql query as string
     *
     * @return string
     */
    public function getQueryString() {
      $jp= new JoinProcessor($this->peer);
      return $this->criteria->getSelectQueryString($this->peer->getConnection(), $this->peer, $jp);
    }
    
    /**
     * execute query
     *
     * @param  int max default 0
     * @return rdbms.ResultSet 
     * @throws lang.IllegalStateException
     */
    public function execute($values= NULL) {
      if (is_null($this->peer)) throw new IllegalStateException('no peer set');
      return $this->peer->doSelect($this->criteria, $values);
    }
    
    /**
     * Retrieve a number of objects from the database
     *
     * @param   int max default 0
     * @return  rdbms.Record[]
     * @throws  lang.IllegalStateException
     */
    public function fetchArray($max= 0) {
      if (is_null($this->peer)) throw new IllegalStateException('no peer set');
      return $this->peer->doSelect($this->criteria);
    }

    /**
     * Returns an iterator for the select statement
     *
     * @return  lang.XPIterator
     * @see     xp://lang.XPIterator
     * @throws  lang.IllegalStateException
     */
    public function fetchIterator() {
      if (is_null($this->peer)) throw new IllegalStateException('no peer set');
      return $this->peer->iteratorFor($this->criteria);
    }

  }
?>
