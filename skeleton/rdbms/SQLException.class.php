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
      $sql;
      
    /**
     * Constructor
     *
     * @param   string message Die Exception-Message
     * @param   string sql default '' Das zuletzt abgesandte SQL
     */
    function __construct($message, $sql= '') {
      parent::__construct($message);
      $this->sql= $sql;
    }
    
    /**
     * "Stack Trace" zurückgeben
     *
     * @return  string der StackTrace, vorformatiert
     */
    function getStackTrace() {
      return (
        parent::getStackTrace().
        "  SQL [\n".$this->sql."\n  ]\n"
      );
    }
  }
?>
