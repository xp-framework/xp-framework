<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.BSDSocket', 'remote.protocol.ByteCountedString');

  /**
   * (Insert class' description here)
   *
   * @purpose  purpose
   */
  class SessionConnection extends Object {
    protected
      $s= NULL;

    /**
     * Session connections
     *
     * @param   string host
     * @param   int port
     */
    public function __construct() {
      $this->socket= new BSDSocket('0.0.0.0', 0);
      $this->socket->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
    }

    /**
     * Read a specified number of bytes
     *
     * @param   int num
     * @return  string 
     */
    protected function readBytes($num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $this->socket->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function connectTo($host, $port) {
      $this->socket->host= $host;
      $this->socket->port= $port;
      $this->socket->connect();
    }
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function command($type, $args= array(), ByteCountedString $bytes= NULL) {
      $data= serialize($args);
      $length= strlen($data);
      // Console::writeLine('>>> ', $type, $args);

      // Send request
      $packet= pack(
        'Nc4Na*', 
        0x3c872748, 
        1,    // vmajor
        0,    // vminor
        $type,
        NULL !== $bytes,
        $length,
        $data
      );
      $this->socket->write($packet);
      $bytes && $bytes->writeTo($this->socket);
      
      // Read response
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/cbytes/Nlength', 
        $this->readBytes(12)
      );

      // Check response
      if (RemoteSessionConstants::STATUS === $header['type']) {
        $data= $this->readBytes($header['length']);
        return unserialize($data);
      } else if (RemoteSessionConstants::VALUE  === $header['type']) {
        $this->readBytes($header['length']);    // Discard
        return unserialize(ByteCountedString::readFrom($this->socket));
      } else if (RemoteSessionConstants::ERROR == $header['type']) {
        $data= $this->readBytes($header['length']);
        throw unserialize($data);
      }
      
      Console::writeLine('??? ', $header);
      throw new IOException('Invalid response: '.xp::stringOf($header));
    }
  }
?>
