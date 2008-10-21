<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.ftp.FtpFile', 'peer.Socket');

  /**
   * Base class for in- and output streams
   *
   * @ext      ftp
   * @see      xp://peer.ftp.FtpOutputStream
   * @see      xp://peer.ftp.FtpIntputStream
   * @purpose  Abstract base class
   */
  abstract class FtpTransferStream extends Object {
    protected
      $file   = NULL,
      $socket = NULL;

    /**
     * Constructor
     *
     * @param   peer.ftp.FtpFile file
     */
    public function __construct(FtpFile $file) {
      with ($conn= $file->getConnection(), $cmd= $this->getCommand()); {

        // Always use binary mode
        // Check for "200 Type set to X"
        $r= $conn->sendCommand('TYPE I');
        sscanf($r[0], '%d %*[^(]', $code);
        if (200 != $code) {
          throw new SocketException('Cannot parse TYPE response '.xp::stringOf($r));
        }

        // Always use passive mode, just to be sure
        // Check for "Entering Passive Mode (h1,h2,h3,h4,p1,p2)."
        $r= $conn->sendCommand('PASV');
        $a= $p= array();
        sscanf($r[0], '%d %*[^(] (%d,%d,%d,%d,%d,%d)', $code, $a[0], $a[1], $a[2], $a[3], $p[0], $p[1]);
        if (227 != $code) {
          throw new SocketException('Cannot parse PASV response '.xp::stringOf($r));
        }
        
        // Open transfer socket
        $this->socket= new Socket(implode('.', $a), $p[0] * 256 + $p[1]);
        $this->socket->connect();
        
        // Begin transfer depending on the direction returned by getCommand()
        // Check for "Opening XXX mode data connection for ..."
        $r= $conn->sendCommand($cmd.' '.$file->getName());
        sscanf($r[0], '%d %*[^(]', $code);
        if (150 != $code) {
          $this->socket->close();
          throw new SocketException('Cannot parse '.$cmd.' response '.xp::stringOf($r));
        }
        
        // Success!
      }
      $this->file= $file;
    }
    
    /**
     * Returns command to send (one of RETR, STOR)
     *
     * @return  string
     */
    protected abstract function getCommand();

    /**
     * Creates a string representation of this file
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->file->toString().'>';
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      $this->socket->close();
    }

    /**
     * Destructor. Ensures transfer socket is closed
     *
     */
    public function __destruct() {
      $this->socket->isConnected() && $this->socket->close();
    }
  }
?>
