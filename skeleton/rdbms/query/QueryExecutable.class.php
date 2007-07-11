<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
    public function execute($values= NULL);
    
    /**
     * get connection for peer
     *
     * @return rdbms.DBConnection
     */
    public function getConnection();

  }
?>
