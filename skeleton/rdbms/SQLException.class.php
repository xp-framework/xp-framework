<?php
/* Diese Klasse ist Teil des XP-Frameworks
 * 
 * $Id$
 */
 
  /**
   * Kapselt SQL-Exceptions
   * 
   * Besonderes:
   * - in e->sql findet sich - falls vorhanden - der Query-String, in e->code der SQL-Returncode
   * - SQL-Returncode ist bspw. bei einer Sybase 1205 für Deadlock
   *
   * @see Exception
   */
  class SQLException extends Exception {
    var 
      $sql,
      $code;
      
    /**
     * Constructor
     *
     * @param   string message Die Exception-Message
     * @param   string sql default '' Das zuletzt abgesandte SQL
     * @param   int code default 0 Der Fehlercode
     */
    function __construct($message, $sql= '', $code= 0) {
      parent::__construct($message);
      $this->sql= $sql;
      $this->code= $code;
    }
  }
?>
