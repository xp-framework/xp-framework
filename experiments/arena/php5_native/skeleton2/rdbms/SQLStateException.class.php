<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.SQLException');

  /**
   * Indicates illegal state (e.g., calling query() before connecting).
   * 
   * @purpose  SQL-Exception
   */
  class SQLStateException extends SQLException {
  
  }
?>
