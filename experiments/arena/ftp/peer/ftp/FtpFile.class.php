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
    private static 
      $sw= NULL;
    
    static function __static() {
      self::$sw= newinstance('lang.Object', array(), '{
        private static $streams= array();
        private $id= NULL;
        
        public function wrap($s) { 
          self::$streams[$s->hashCode()]= $s;
          return "iostr://".$s->hashCode(); 
        }
        
        function stream_open($path, $mode, $options, $opened_path) {
          sscanf($path, "iostr://%[^$]", $this->id);
          if (!isset(self::$streams[$this->id])) {
            throw new FileNotFoundException("No such iostr ".$this->id);
          }
          return TRUE;
        }
        
        function stream_read($count) {
          return self::$streams[$this->id]->read($count);
        }

        function stream_eof() {
          return 0 === self::$streams[$this->id]->available();
        }
        
        function stream_write($data) {
          self::$streams[$this->id]->write($data);
          return strlen($data);
        }

        function stream_close() {
          self::$streams[$this->id]->close();
          unset(self::$streams[$this->id]);
        }

        public function stream_stat() {
          return array("size" => self::$streams[$this->id]->available());
        }
      }');
      stream_wrapper_register('iostr', get_class(self::$sw));
    }

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
      $sw= fopen(self::$sw->wrap($in), 'rb');
      if ($listener) {
        $stat= fstat($sw);
        $size= isset($stat['size']) ? $stat['size'] : -1;
        $transfer= new FtpUpload($this, $in);
        $r= ftp_nb_fput(
          $this->connection->handle, 
          $this->name,
          $sw,
          $mode
        );
        $listener->started($transfer);
        while (FTP_MOREDATA === $r) {
          if ($transfer->aborted()) {
            $r= -1;
            break;
          }
          $r= ftp_nb_continue($this->connection->handle);
          $listener->transferred($transfer, ftell($sw), $size);
        }
        fclose($sw);
        if (FTP_FINISHED === $r) {            // Transfer finished normally
          $listener->completed($transfer);
          return $this;
        } else if (-1 === $r) {               // Aborted
          $e= new SocketException(sprintf(
            'Transfer from %s to %s (mode %s) was aborted',
            $in->toString(), $this->name, $mode
          ));
          $listener->aborted($transfer);
        } else {                              // Failed
          $e= new SocketException(sprintf(
            'Could not put %s to %s using mode %s',
            $in->toString(), $this->name, $mode
          ));          
          $listener->failed($transfer, $e);
        }
      } else {
        $r= ftp_fput(
          $this->connection->handle, 
          $this->name, 
          $sw, 
          $mode
        );
        fclose($sw);
        if (TRUE === $r) return $this;

        $e= new SocketException(sprintf(
          'Could not put %s to %s using mode %s',
          $in->toString(), $this->name, $mode
        ));          
      }

      throw $e;
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
      $sw= fopen(self::$sw->wrap($out), 'wb');
      if ($listener) {
        $size= $this->size;
        $transfer= new FtpDownload($this, $out);
        $r= ftp_nb_fget(
          $this->connection->handle, 
          $sw,
          $this->name,
          $mode
        );
        $listener->started($transfer);
        while (FTP_MOREDATA === $r) {
          if ($transfer->aborted()) {
            $r= -1;
            break;
          }
          $r= ftp_nb_continue($this->connection->handle);
          $listener->transferred($transfer, ftell($sw), $size);
        }
        fclose($sw);
        if (FTP_FINISHED === $r) {            // Transfer finished normally
          $listener->completed($transfer);
          return $this;
        } else if (-1 === $r) {            // Aborted
          $e= new SocketException(sprintf(
            'Transfer from %s to %s (mode %s) was aborted',
            $this->name, $out->toString(), $mode
          ));
          $listener->aborted($transfer);
        } else {                              // Failed
          $e= new SocketException(sprintf(
            'Could not put %s to %s using mode %s',
            $this->name, $out->toString(), $mode
          ));          
          $listener->failed($transfer, $e);
        }
      } else {
        $r= ftp_fget(
          $this->connection->handle, 
          $sw,
          $this->name, 
          $mode
        );
        fclose($sw);
        if (TRUE === $r) return $out;

        $e= new SocketException(sprintf(
          'Could not get %s to %s using mode %s',
          $this->name, $out->toString(), $mode
        ));         
      }

      throw $e;
    }
  }
?>
