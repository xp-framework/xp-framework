<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('peer.ftp.FtpEntry', 'io.streams.InputStream', 'io.streams.OutputStream');

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
     * @throws  peer.SocketException in case of an I/O error
     */
    public function uploadFrom(InputStream $in, $mode= FTP_ASCII) {
      $r= ftp_fput(
        $this->connection->handle, 
        $this->name, 
        $sw= fopen(self::$sw->wrap($in), 'rb'), 
        $mode
      );
      fclose($sw);
      if (TRUE === $r) return;

      throw new SocketException(sprintf(
        'Could not put %s to %s using mode %s',
        $in->toString(), $remote, $mode
      ));
    }

    /**
     * Upload to this file from an input stream
     *
     * @param   io.streams.OutputStream out
     * @param   int mode default FTP_ASCII
     * @return  io.streams.OutputStream the output stream passed
     * @throws  peer.SocketException in case of an I/O error
     */
    public function downloadTo(OutputStream $out, $mode= FTP_ASCII) {
      $r= ftp_fget(
        $this->connection->handle, 
        $sw= fopen(self::$sw->wrap($out), 'wb'),
        $this->name, 
        $mode
      );
      fclose($sw);
      if (TRUE === $r) return $out;

      throw new SocketException(sprintf(
        'Could not get %s to %s using mode %s',
        $remote, $out->toString, $mode
      ));
    }
  }
?>
