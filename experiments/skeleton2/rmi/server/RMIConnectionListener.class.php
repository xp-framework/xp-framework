<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.server.ConnectionListener',
    'rmi.RMIUnmarshalException',
    'rmi.server.RemoteException',
    'rmi.server.NoSuchObjectException',
    'rmi.server.RMIRegistry'
  );

  /**
   * Connection listener
   *
   * @purpose  Listener
   */
  class RMIConnectionListener extends ConnectionListener {
    public
      $cat  = NULL;
      
    /**
     * Set a logcategory for tracing
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setTrace(LogCategory $cat) {
      $this->cat= $cat;
    }
  
    /**
     * Method to be triggered when a client connects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function connected(ConnectionEvent $event) {
      $this->cat && $this->cat->info('ConnectionListener::connected()');
    }
    
    /**
     * Prcoess request
     *
     * @access  private
     * @param   string str
     * @return  &mixed data
     */
    private function process($str) {
      $length= hexdec(trim(substr($str, 0x1, 0x8)));
      $data= substr($str, 0x9, $length);
      $checksum= substr(chop($str), -0x20);
      
      // Checks
      if ($length != strlen($data)) {
        throw (new RMIUnmarshalException(
          'Missing data, expecting '.$length.' bytes, got '.strlen($data)
        ));
      }

      $hash= md5($data);
      if ($hash != $checksum) {
        throw (new RMIUnmarshalException(
          'Checksum mismatch, expecting '.$checksum.', have '.$hash
        ));
      }

      // Ask registry
      list($classname, $name, $arg)= explode(':', $data, 3);
      $registry= RMIRegistry::getInstance();
      if (NULL === ($obj= $registry->get($classname))) {
        throw (new NoSuchObjectException('No such object "'.$classname.'" registered'));
      }
      
      switch ($str{0}) {
        case 'S':
          $obj->{$name}= unserialize($arg);
          $this->cat && $this->cat->debugf('@@ %s->%s= %s', $classname, $name, var_export(unserialize($arg), 1));
          $registry->update($classname, $obj);
          return NULL;

        case 'G':
          $this->cat && $this->cat->debugf('@@ %s= %s->%s', var_export($obj->{$name}, 1), $classname, $name);
          return $obj->{$name};

        case 'I':
          $this->cat && $this->cat->debugf('@@ %s->%s(%s)', $classname, $name, var_export(unserialize($arg), 1));
          $r= call_user_func_array(array(&$obj, $name), unserialize($arg));
          $registry->update($classname, $obj);
          return $r;
      }
      
      // Will never reach this point
    }
    
    /**
     * Helper method
     *
     * @access  private
     * @param   &io.Stream stream
     * @param   string identifier
     * @param   string data
     * @return  bool success
     */
    private function respond(Stream $stream, $identifier, $data) {
      $this->cat && $this->cat->infof(
        'ConnectionListener::respond(%s %s)', 
        $identifier,
        addcslashes($data, "\0..\37")
      );

      $r= $stream->write(
        $identifier.
        str_pad(dechex(strlen($data)), 0x8, ' ', STR_PAD_RIGHT).
        $data.
        md5($data).
        "\r\n"
      );
      $this->cat && $this->cat->debug('ConnectionListener::respond.return', $r);
      
      return $r;
    }
    
    /**
     * Method to be triggered when a client has sent data
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function data(ConnectionEvent $event) { 
      if (!strstr('GSI', $event->data{0})) {    // Ignore corrupt data
        $this->cat && $this->cat->debug('Corrupt >', addcslashes($event->data, "\0..\37"), '<');
        $event->stream->close();
        return FALSE;
      }

      $this->cat && $this->cat->infof(
        'ConnectionListener::data(%s)', 
        addcslashes($event->data, "\0..\37")
      );
      
      try {
        $return= self::process($event->data);
      } catch (XPException $e) {
        if ($this->cat) {
          $this->cat->warn('ConnectionListener::process(', $e->getStackTrace(), ')');
        } else {
          $e->printStackTrace();    // goes to STDOUT
        }
        self::respond($event->stream, 'E', serialize($e));
        return;
      }
      
      self::respond($event->stream, $event->data{0}, serialize($return));
    }
    
    /**
     * Method to be triggered when a client disconnects
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function disconnected(ConnectionEvent $event) { 
      $this->cat && $this->cat->info('ConnectionListener::disconnected()');
    }
    
    /**
     * Method to be triggered when a communication error occurs
     *
     * @access  public
     * @param   &peer.server.ConnectionEvent event
     */
    public function error(ConnectionEvent $event) { 
      if ($this->cat) {
        $this->cat->warn('ConnectionListener::error(', $event->data->getStackTrace(), ')');
      } else {
        $event->data->printStackTrace();
      }
    }
  }
?>
