<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpTransfer');

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  purpose
   */
  interface FtpTransferListener {
  
    /**
     * Called when a transfer is started
     *
     * @param   peer.ftp.FtpTransfer transfer
     */
    public function started(FtpTransfer $transfer);

    /**
     * Called while transferring
     *
     * @param   peer.ftp.FtpTransfer transfer
     */
    public function transferred(FtpTransfer $transfer);

    /**
     * Called when a transfer has been completed.
     *
     * @param   peer.ftp.FtpTransfer transfer
     */
    public function completed(FtpTransfer $transfer);

    /**
     * Called when a transfer has been aborted
     *
     * @param   peer.ftp.FtpTransfer transfer
     */
    public function aborted(FtpTransfer $transfer);

    /**
     * Called when a transfer fails
     *
     * @param   peer.ftp.FtpTransfer transfer
     * @param   lang.XPException cause
     */
    public function failed(FtpTransfer $transfer, XPException $cause);
  
  }
?>
