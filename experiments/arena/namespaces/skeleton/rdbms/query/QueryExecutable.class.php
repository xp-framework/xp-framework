<?php
/* This class is part of the XP framework
 *
 * $Id: QueryExecutable.class.php 10778 2007-07-11 15:45:40Z ruben $ 
 */

  namespace rdbms::query;

  /**
   * interface for all query classes
   *
   * @see      xp://rdbms.query.Query
   * @purpose  rdbms.query
   */
  interface QueryExecutable {
    
    /**
     * execute query
     *
     * @param  mixed[] values
     * @return mixed
     * @throws lang.IllegalStateException
     */
    public function execute($values= );
    
    /**
     * get connection for peer
     *
     * @return rdbms.DBConnection
     */
    public function getConnection();

  }
?>
