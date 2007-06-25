<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.server.ServerProtocol', 
    'remote.protocol.Serializer',
    'session.RemoteSessionConstants'
  );

  /**
   * HTTP protocol implementation
   *
   * @purpose  Protocol
   */
  class SessionProtocol extends Object implements ServerProtocol {
    protected static $types= array(
      RemoteSessionConstants::INIT    => 'initializeSession',
      RemoteSessionConstants::CREATE  => 'createSession',
      RemoteSessionConstants::VALID   => 'sessionIsValid',
      RemoteSessionConstants::KILL    => 'destroySession',
      RemoteSessionConstants::RESET   => 'resetSession',

      RemoteSessionConstants::EXISTS  => 'sessionValueExists',
      RemoteSessionConstants::WRITE   => 'writeToSession',
      RemoteSessionConstants::READ    => 'readFromSession',
      RemoteSessionConstants::DELETE  => 'deleteFromSession',
      RemoteSessionConstants::KEYS    => 'sessionKeys',
    );
    
    /**
     * Initialize Protocol
     *
     * @return  bool
     */
    public function initialize() {
      $this->serializer= new Serializer();

      // Encode our host IP into the identifier
      $ip= $this->server->socket->host;
      $this->identifier= implode('', array_map('dechex', explode('.', $ip)));
    }

    /**
     * Handle client connect
     *
     * @param   peer.Socket socket
     */
    public function handleConnect($socket) {
      // Intentionally empty
    }

    /**
     * Handle client disconnect
     *
     * @param   peer.Socket socket
     */
    public function handleDisconnect($socket) {
      $socket && $socket->close();
    }
    
    /**
     * Supply persistence handler
     *
     * @param   persist.SessionPersistence persist
     */
    public function setPersistence(SessionPersistence $persist) {
      $this->persist= $persist;
    }

    /**
     * Read a specified number of bytes
     *
     * @param   int num
     * @return  string 
     */
    protected function readBytes(Socket $s, $num) {
      $return= '';
      while (strlen($return) < $num) {
        if (0 == strlen($buf= $s->readBinary($num - strlen($return)))) return;
        $return.= $buf;
      }
      return $return;
    }
    
    public function createSession($timeout) {
      $id= $this->persist->create($this->identifier, $timeout);
      $this->persist->save($id);
      return $id;
    }

    public function initializeSession($id) {
      $this->persist->load($id);
      return TRUE;
    }

    public function destroySession($id) {
      $this->persist->terminate($id);
      return TRUE;
    }

    public function resetSession($id) {
      $this->persist->reset($id);
      $this->persist->save($id);
      return TRUE;
    }

    public function sessionIsValid($id) {
      return $this->persist->valid($id);
    }
 
    public function sessionValueExists($id, $name) {
      return $this->persist->exists($id, $name);
    }

    public function readFromSession($id, $name) {
      return $this->persist->read($id, $name);
    }

    public function writeToSession($id, $name, $value) {
      $this->persist->write($id, $name, $value);
      $this->persist->save($id);
      return TRUE;
    }

    public function deleteFromSession($id, $name) {
      $this->persist->delete($id, $name);
      $this->persist->save($id);
      return TRUE;
    }

    public function sessionKeys($id) {
      return $this->persist->keys($id);
    }
 
    /**
     * Handle client data
     *
     * @param   peer.Socket socket
     * @return  mixed
     */
    public function handleData($socket) {
      try {
        $header= unpack(
          'Nmagic/cvmajor/cvminor/ctype/ctran/Nlength', 
          $this->readBytes($socket, 12)
        );
      } catch (IOException $e) {
        // Ignore Console::$err->writeLine($e);
        return $socket->close();
      }
      
      if (!$header['length']) {
        return $socket->close();
      }

      $args= $this->serializer->valueOf(new SerializedData($this->readBytes($socket, $header['length'])));      
      try {
        $return= call_user_func_array(
          array($this, self::$types[$header['type']]), 
          $args
        );
        $type= RemoteSessionConstants::OK;
      } catch (Throwable $e) {
        $return= $e;
        $type= RemoteSessionConstants::ERROR;
      }

      $data= $this->serializer->representationOf($return);
      $length= strlen($data);
      
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
      $socket->write($packet);
    }

    /**
     * Handle I/O error
     *
     * @param   peer.Socket socket
     * @param   lang.XPException e
     */
    public function handleError($socket, $e) {
      Console::$err->writeLine('* ', $socket->host, '~', $e);
      $socket->close();
    }
  }
?>
