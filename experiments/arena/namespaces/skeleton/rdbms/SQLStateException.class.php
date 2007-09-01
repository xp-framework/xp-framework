<?php
/* This class is part of the XP framework
 *
 * $Id: SQLStateException.class.php 2389 2003-09-27 12:44:29Z friebe $ 
 */

  namespace rdbms;

  uses('rdbms.SQLException');

  /**
   * Indicates illegal state (e.g., calling query() before connecting).
   * 
   * @purpose  SQL-Exception
   */
  class SQLStateException extends SQLException {
  
  }
?>
