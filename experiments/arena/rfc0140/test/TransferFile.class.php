<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'test.AbstractFtpTestCommand',
    'peer.ftp.FtpTransferListener'
  );

  /**
   * Transfer a file to or from the FTP server,
   *
   * @purpose  Command
   */
  abstract class TransferFile extends AbstractFtpTestCommand {
    protected
      $local    = NULL,
      $listener = NULL,
      $remote   = '';

    /**
     * Set whether to listen
     *
     */
    #[@arg]
    public function setListener() {
      $this->listener= newinstance('peer.ftp.FtpTransferListener', array($this->out), '{
        protected
          $out= NULL;
        
        public function __construct($out) {
          $this->out= $out;
        }
        
        public function started(FtpTransfer $transfer) {
          $this->out->write("Started ", $transfer, " [");
        }

        public function transferred(FtpTransfer $transfer, $bytes) {
          $this->out->write(".");
        }

        public function completed(FtpTransfer $transfer) {
          $this->out->writeLine("] completed");
        }

        public function aborted(FtpTransfer $transfer) {
          $this->out->writeLine("] aborted");
        }

        public function failed(FtpTransfer $transfer, XPException $cause) {
          $this->out->writeLine("] failed (", $cause->compoundMessage(), ")");
        }
      }');
    }
  }
?>
