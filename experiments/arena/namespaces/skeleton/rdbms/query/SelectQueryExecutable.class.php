<?php
/* This class is part of the XP framework
 *
 * $Id: SelectQueryExecutable.class.php 10778 2007-07-11 15:45:40Z ruben $ 
 */

  namespace rdbms::query;
  uses('rdbms.query.QueryExecutable');

  /**
   * Interface for all query classes that select rows.
   * All Methods of SetOperation take SelectQueryExecutable
   * implementations as argument.
   *
   * @see      xp://rdbms.query.Query
   * @see      xp://rdbms.query.SelectQuery
   * @see      xp://rdbms.query.SetOperation
   * @purpose  rdbms.query
   */
  interface SelectQueryExecutable extends QueryExecutable {
    
    /**
     * get sql query as string
     *
     * @return string
     */
    public function getQueryString();
    
    /**
     * Retrieve a number of objects from the database
     * If max is 0, no limitation will be asumend
     *
     * @param   int max default 0
     * @return  rdbms.Record[]
     * @throws  lang.IllegalStateException
     */
    public function fetchArray($max= 0);

    /**
     * Returns an iterator for the select statement
     *
     * @return  lang.XPIterator
     * @see     xp://lang.XPIterator
     * @throws  lang.IllegalStateException
     */
    public function fetchIterator();

  }
?>
