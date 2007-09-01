<?php
/* This class is part of the XP framework
 *
 * $Id: SQLConnectionClosedException.class.php 7136 2006-06-12 12:33:03Z friebe $ 
 */

  namespace rdbms;

  uses('rdbms.SQLStatementFailedException');

  /**
   * Indicates the connection was lost during an SQL query
   *
   * @see      rfc://0058
   * @purpose  Exception
   */
  class SQLConnectionClosedException extends SQLStatementFailedException {
  
  }
?>
