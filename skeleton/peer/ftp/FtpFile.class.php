<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ftp.FtpEntry', 
    'peer.ftp.FtpTransferListener',
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
      if (FALSE === ftp_delete($this->connection->handle, $this->name)) {
        throw new IOException('Could not delete file "'.$this->name.'"');
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
      if ($listener) {
        $transfer= create(new FtpUpload($this, $in))->withListener($listener)->start($mode);
        while (!$transfer->complete()) {
          $transfer->perform();
        }
        
        if ($transfer->aborted()) {
          throw new SocketException(sprintf(
            'Transfer from %s to %s (mode %s) was aborted',
            $in->toString(), $this->name, $mode
          ));
        }
      } else {
        $r= ftp_fput(
          $this->connection->handle, 
          $this->name, 
          $sw= Streams::readableFd($in), 
          $mode
        );
        fclose($sw);
        if (TRUE !== $r) {
          throw new SocketException(sprintf(
            'Could not put %s to %s using mode %s',
            $in->toString(), $this->name, $mode
          ));
        }
      }
      
      // Reload file details
      $f= ftp_rawlist($this->connection->handle, $this->name);
      if (1 != sizeof($f)) {
        throw new ProtocolException('File '.$this->name.' not existant after uploading');
      }
      with ($e= $this->connection->parser->entryFrom($f[0], $this->connection, rtrim(dirname($this->name), '/').'/')); {
        $this->permissions= $e->permissions;
        $this->numlinks= $e->numlinks;
        $this->user= $e->user;
        $this->group= $e->group;
        $this->size= $e->size;
        $this->date= $e->date;
      }
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
     * Upload to this file from an input stream
     *
     * @param   io.streams.OutputStream out
     * @param   int mode default FTP_ASCII
     * @param   peer.ftp.FtpTransferListener listener default NULL
     * @return  io.streams.OutputStream the output stream passed
     * @throws  peer.SocketException in case of an I/O error
     */
    public function downloadTo(OutputStream $out, $mode= FTP_ASCII, FtpTransferListener $listener= NULL) {
      if ($listener) {
        $transfer= create(new FtpDownload($this, $out))->withListener($listener)->start($mode);
        while (!$transfer->complete()) {
          $transfer->perform();
        }
        
        if ($transfer->aborted()) {
          throw new SocketException(sprintf(
            'Transfer from %s to %s (mode %s) was aborted',
            $in->toString(), $this->name, $mode
          ));
        }
      } else {
        $r= ftp_fget(
          $this->connection->handle, 
          $sw= Streams::writeableFd($out),
          $this->name, 
          $mode
        );
        fclose($sw);
        if (TRUE !== $r) {
          throw new SocketException(sprintf(
            'Could not get %s to %s using mode %s',
            $this->name, $out->toString(), $mode
          )); 
        }
      }
      return $out;
    }
  }
?>
