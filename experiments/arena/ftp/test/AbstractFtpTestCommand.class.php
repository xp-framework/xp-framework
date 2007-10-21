<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'peer.ftp.FtpConnection'
  );

  /**
   * Abstract base class for all
   *
   * @see      xp://peer.ftp.FtpConnection
   * @purpose  Command
   */
  abstract class AbstractFtpTestCommand extends Command {
    protected
      $conn= NULL;

    /**
     * Set connection dsn
     *
     * @param  string dsn 
     */
    #[@arg(position= 0)]
    public function setDsn($dsn) {
      $this->conn= create(new FtpConnection($dsn))->connect();
    }

    /**
     * Destructor. Ensures connection is closed.
     *
     */
    public function __destruct() {
      $this->conn && $this->conn->close();
    }
  }
?>
