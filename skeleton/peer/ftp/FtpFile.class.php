<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ftp.FtpEntry', 
    'peer.ftp.FtpTransferListener',
    'peer.ftp.FtpInputStream',
    'peer.ftp.FtpOutputStream',
    'peer.ftp.FtpUpload',
    'peer.ftp.FtpDownload',
    'io.streams.Streams',
    'io.streams.InputStream', 
    'io.streams.OutputStream'
  );

  /**
   * FTP file
   *
   * @see      xp://peer.ftp.FtpDir#getFile
   * @purpose  FtpEntry implementation
   */
  class FtpFile extends FtpEntry {

    /**
     * Delete this entry
     *
     * @throws  io.IOException in case of an I/O error
     */
    public function delete() {
      $this->connection->expect($this->connection->sendCommand('DELE %s', $this->name), array(250));
    }
    
    /**
     * Returns an input stream to read from this file
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return new FtpInputStream($this);
    }

    /**
     * Returns an output stream to write to this file
     *
     * @return  io.streams.OutputStream
     */
    public function getOutputStream() {
      return new FtpOutputStream($this);
    }
    
    /**
     * Reload this file's details.
     *
     * @param   string state
     * @throws  peer.SocketException in case of an I/O error
     */
    public function refresh($state) {
      $list= $this->connection->listingOf($this->name);
      if (NULL === $list || 1 != sizeof($list)) {
        throw new ProtocolException('File '.$this->name.' not existant after '.$state);
      }
      with ($e= $this->connection->parser->entryFrom($list[0], $this->connection, rtrim(dirname($this->name), '/').'/')); {
        $this->permissions= $e->permissions;
        $this->numlinks= $e->numlinks;
        $this->user= $e->user;
        $this->group= $e->group;
        $this->size= $e->size;
        $this->date= $e->date;
      }
    }

    /**
     * Upload to this file from an input stream
     *
     * @param   io.streams.InputStream in
     * @param   int mode default FTP_ASCII
     * @param   peer.ftp.FtpTransferListener listener default NULL
     * @return  peer.ftp.FtpFile this file
     * @throws  peer.SocketException in case of an I/O error
     */
    public function uploadFrom(InputStream $in, $mode= FTP_ASCII, FtpTransferListener $listener= NULL) {
      $transfer= create(new FtpUpload($this, $in))->withListener($listener)->start($mode);
      while (!$transfer->complete()) $transfer->perform();

      if ($transfer->aborted()) {
        throw new SocketException(sprintf(
          'Transfer from %s to %s (mode %s) was aborted',
          $in->toString(), $this->name, $mode
        ));
      }
      
      $this->refresh('upload');
      return $this;
    }
    
    /**
     * Starts a transfer
     *
     * @see     xp://peer.ftp.FtpDownload#to
     * @see     xp://peer.ftp.FtpUpload#from
     * @param   peer.ftp.FtpTransfer transfer
     * @param   int mode default FTP_ASCII
     * @return  peer.ftp.FtpTransfer 
     */
    public function start(FtpTransfer $transfer, $mode= FTP_ASCII) {
      $transfer->setRemote($this);
      $transfer->start($mode);
      return $transfer;
    }

    /**
     * Download this file to an output stream
     *
     * @param   io.streams.OutputStream out
     * @param   int mode default FTP_ASCII
     * @param   peer.ftp.FtpTransferListener listener default NULL
     * @return  io.streams.OutputStream the output stream passed
     * @throws  peer.SocketException in case of an I/O error
     */
    public function downloadTo(OutputStream $out, $mode= FTP_ASCII, FtpTransferListener $listener= NULL) {
      $transfer= create(new FtpDownload($this, $out))->withListener($listener)->start($mode);
      while (!$transfer->complete()) $transfer->perform();

      if ($transfer->aborted()) {
        throw new SocketException(sprintf(
          'Transfer from %s to %s (mode %s) was aborted',
          $in->toString(), $this->name, $mode
        ));
      }
      return $out;
    }
  }
?>
