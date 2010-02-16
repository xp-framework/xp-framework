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
     * @param  var[] values
     * @return var
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
