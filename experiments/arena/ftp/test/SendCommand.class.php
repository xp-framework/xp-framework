<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('test.AbstractFtpTestCommand');

  /**
   * Sends an arbitrary command to the FTP server
   *
   * @see      xp://peer.ftp.FtpDir#entries
   * @purpose  Command
   */
  class SendCommand extends AbstractFtpTestCommand {
    protected
      $args= array();

    /**
     * Select all args
     *
     * @param   string[] args
     */
    #[@args(select= '[1..]')]
    public function allArgs($args) {
      $this->args= $args;
      $this->out->writeLine('Arguments: ', $this->args);
    }
    
    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine(call_user_func_array(array($this->conn, 'sendCommand'), $this->args));
    }
  }
?>
