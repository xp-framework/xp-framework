<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
