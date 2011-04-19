<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.SQLStatementFailedException');

  /**
   * Indicates a deadlock occured
   * 
   * @purpose  SQL-Exception
   */
  class SQLDeadlockException extends SQLStatementFailedException {

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      return sprintf(
        "Exception %s (deadlock#%s: %s) {\n".
        "  %s\n".
        "}\n",
        $this->getClassName(),
        $this->errorcode,
        $this->message,
        $this->sql
      );
    }
  }
?>
