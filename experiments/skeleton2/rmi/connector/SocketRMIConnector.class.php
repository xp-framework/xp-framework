<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rmi.RMIConnector',
    'rmi.RMIException',
    'rmi.RMIServerException',
    'rmi.RMIUnmarshalException',
    'rmi.server.NoSuchObjectException',
    'peer.Socket'
  );

  /**
   * RMI Connector
   *
   * @see      xp://rmi.RMIObject
   * @purpose  Connector
   */
  class SocketRMIConnector extends RMIConnector {
    public
      $socket   = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &peer.Socket socket
     */
    public function __construct(Socket $socket) {
      $this->socket= $socket;
    }
    
    /**
     * Communication helper method
     *
     * @access  protected
     * @param   string identifier
     * @param   string data
     * @return  &mixed value
     * @throws  rmi.RMIUnmarshalException
     * @throws  rmi.RMIException in case of communications error
     */
    protected function comm($identifier, $data) {
      try {
        do {
          if (!$this->socket->isConnected()) {
            if (!$this->socket->connect()) break;
          }
        
          // Write the identifier, followed by the length of the data,
          // the data itself, a checksum (always 32 bytes in length) and
          // a CRLF.
          //
          // Examples:
          // S27      rmi.RMIObject:value=s:11:"hello world";d438808d29cdd1b604b412b6442816d0
          // G13      rmi.RMIObject:value:917337b1be9de276494268253c3f4a92
          // I1a      rmi.RMIObject:hello:a:0:{}36f21ff51d97e07e6877cf1d23a6bfb9
          if (!$this->socket->write(
            $identifier.
            str_pad(dechex(strlen($data)), 0x8, ' ', STR_PAD_RIGHT).
            $data.
            md5($data).
            "\r\n"
          )) break;

          // Read the header. This will contain an identifier as its
          // first byte and 8 padded bytes containing a hexadecimal number
          // specifying data length. The identifier is either identical
          // to the identifier sent or the character "E" to indicate an
          // exception occured on the server side.
          if (!($header= $this->socket->read(0xA))) break;
          if (($identifier != $header{0}) && ('E' != $header{0})) {
            throw (new RMIUnmarshalException(
              'Expected "'.$identifier.'", got "'.$header.'"'
            ));
          }
          $length= hexdec(trim(substr($header, 1)));

          // Read data and verify checksum
          $data= $this->socket->read($length+ 1);
          if (strlen($data) != $length) {
            throw (new RMIUnmarshalException(
              'Missing data, expecting '.$length.' bytes, got '.strlen($data)
            ));
          }

          $checksum= $this->socket->read(0x21);
          $hash= md5($data);
          if ($hash != $checksum) {
            throw (new RMIUnmarshalException(
              'Checksum mismatch, expecting '.$checksum.', have '.$hash
            ));
          }
        } while(0);
      } catch (SocketException $e) {
        $this->socket->close();
        throw (new RMIException($e->message));
      }
      
      // If the method on the server threw a rmi.RMIRemoteException,
      // the data returned will contain an exception object.
      if ('E' == $header{0}) {
        throw (new RMIServerException('Invokation failed', unserialize($data)));
      }
      
      return unserialize($data);
    }
        
    /**
     * Get a value by its name
     *
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @return  &mixed value
     * @throws  rmi.RMIException to indicate failure
     */
    public function getValue(RMIObject $object, $name) {  
      return $this->comm('G', $object->getClassName().':'.$name.':');
    }
    
    /**
     * Set a value by its name
     *
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @param   &mixed value
     */
    public function setValue(RMIObject $object, $name, $value) {
      $this->comm('S', $object->getClassName().':'.$name.':'.serialize($value));
    }
    
    /**
     * Invoke a method
     *
     * @access  public
     * @param   &rmi.RMIObject object
     * @param   string name
     * @param   &array args
     * @return  &mixed value
     */
    public function invokeMethod(RMIObject $object, $name, $args) {
      return $this->comm('I', $object->getClassName().':'.$name.':'.serialize($args));
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      unset($this->socket);
    }
  }
?>
