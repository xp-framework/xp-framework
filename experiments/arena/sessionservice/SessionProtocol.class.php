<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.server.ServerProtocol', 
    'remote.protocol.ByteCountedString',
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
    
    protected function createSession($timeout) {
      $id= $this->persist->create($this->identifier, $timeout);
      $this->persist->save($id);
      return array(RemoteSessionConstants::STATUS, $id);
    }

    protected function initializeSession($id) {
      $this->persist->load($id);
      return array(RemoteSessionConstants::STATUS, TRUE);
    }

    protected function destroySession($id) {
      $this->persist->terminate($id);
      return array(RemoteSessionConstants::STATUS, TRUE);
    }

    protected function resetSession($id) {
      $this->persist->reset($id);
      $this->persist->save($id);
      return array(RemoteSessionConstants::STATUS, TRUE);
    }

    protected function sessionIsValid($id) {
      return array(RemoteSessionConstants::STATUS, $this->persist->valid($id));
    }
 
    protected function sessionValueExists($id, $name) {
      return array(RemoteSessionConstants::STATUS, $this->persist->exists($id, $name));
    }

    protected function readFromSession($id, $name) {
      return array(RemoteSessionConstants::VALUE, NULL, new ByteCountedString($this->persist->read($id, $name)));   // FIXME: FALSE for -NOKEY
    }

    protected function writeToSession($id, $name, $value) {
      $this->persist->write($id, $name, $value);
      $this->persist->save($id);
      return array(RemoteSessionConstants::STATUS, TRUE);
    }

    protected function deleteFromSession($id, $name) {
      $this->persist->delete($id, $name);
      $this->persist->save($id);
      return array(RemoteSessionConstants::STATUS, TRUE);
    }

    protected function sessionKeys($id) {
      return array(RemoteSessionConstants::STATUS, $this->persist->keys($id));
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
          'Nmagic/cvmajor/cvminor/ctype/cbytes/Nlength', 
          $this->readBytes($socket, 12)
        );
      } catch (IOException $e) {
        // Ignore Console::$err->writeLine($e);
        return $socket->close();
      }
      
      if (!$header['length']) {
        return $socket->close();
      }

      $args= unserialize($this->readBytes($socket, $header['length']));
      if ($header['bytes']) {
        $args[]= ByteCountedString::readFrom($socket);
      }
      try {
        $return= call_user_func_array(
          array($this, self::$types[$header['type']]), 
          $args
        );
        $type= $return[0];
        $data= serialize($return[1]);
      } catch (Throwable $e) {
        $data= serialize($e);
        $type= RemoteSessionConstants::ERROR;
      }

      $packet= pack(
        'Nc4Na*', 
        0x3c872748, 
        1,    // vmajor
        0,    // vminor
        $type,
        isset($return[2]),
        strlen($data),
        $data
      );
      $socket->write($packet);
      $return[2] && $return[2]->writeTo($socket);
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
