<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.protocol.Serializer', 'peer.BSDSocket');

  /**
   * (Insert class' description here)
   *
   * @purpose  purpose
   */
  class SessionConnection extends Object {
    protected
      $s= NULL;

    public function __construct($host, $port) {
      $this->socket= new BSDSocket($host, $port);
      $this->socket->setOption(getprotobyname('tcp'), TCP_NODELAY, TRUE);
      $this->serializer= new Serializer();
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

    public function connect($id= NULL) {
      $this->socket->connect();
    }
  
    public function command($type, $args= array()) {
      $data= $this->serializer->representationOf($args);
      $length= strlen($data);
      // Console::writeLine('>>> ', $type, $args);

      // Send request
      $packet= pack(
        'Nc4Na*', 
        0x3c872748, 
        1,    // vmajor
        0,    // vminor
        $type,
        FALSE,
        $length,
        $data
      );
      $this->socket->write($packet);
      
      // Read response
      $header= unpack(
        'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
        $this->readBytes(12)
      );

      // Check response
      if (RemoteSessionConstants::OK === $header['type']) {
        $data= $this->readBytes($header['length']);
        return $this->serializer->valueOf(new SerializedData($data));
      } else if (RemoteSessionConstants::ERROR == $header['type']) {
        $data= $this->readBytes($header['length']);
        throw $this->serializer->valueOf(new SerializedData($data));
      }
      
      Console::writeLine('??? ', $header);
      throw new IOException('Invalid response: '.xp::stringOf($header));
    }
  }
?>
