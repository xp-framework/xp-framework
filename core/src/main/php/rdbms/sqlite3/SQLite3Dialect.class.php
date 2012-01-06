<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses('rdbms.sqlite.SQLiteDialect');

  /**
   * helps to build functions for different SQL servers
   *
   */
  class SQLite3Dialect extends SQLiteDialect {

    /**
     * register sql standard functions for a connection
     *
     * @param   db handel conn
     */
    public function registerCallbackFunctions($conn) {
      $conn->createFunction('cast', array($this, '_cast'), 2);
      $conn->createFunction('sign', array($this, '_sign'), 1);
      $conn->createFunction('dateadd', array($this, '_dateadd'), 3);
      $conn->createFunction('locate',  array($this, '_locate'), 3);
      $conn->createFunction('nullif',  array($this, '_nullif'), 2);
    }
  }
?>
