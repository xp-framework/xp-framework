<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
 
  uses('io.IOException');
  
  class Socket extends Object {
    var
      $host,
      $port,
      $timeout= 2,
      $_hSocket;
    
    /**
     * Gibt zurück, ob eine Connection offen ist
     *
     * @access  public
     * @return  bool Connection ist offen
     */
    function isConnected() {
      return (bool)is_resource($this->_hSocket);
    }
    
    /**
     * Verbindung herstellen
     *
     * @access  public
     * @return  bool Status
     * @throws  IllegalArgumentException wenn host oder port leer
     * @throws  IOException wenn der Connect nicht (innerhalb des Timeouts) hergestellt werden konnte
     */
    function connect() {
      if ($this->isConnected()) return 1;
      
      if (empty($this->host) || empty($this->port)) return (
        throw(new IllegalArgumentException('Socket::connect ===> no host specified'))
      );
      
      if (!$this->_hSocket= fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout)) return (
        throw(new IOException(sprintf(
          'sock::failed connecting to %s:%s within %s seconds [%d: %s]',
          $this->host,
          $this->port,
          $this->timeout,
          $errno,
          $errstr
        )))
      );
      return 1;
    }
    
    /**
     * Vom Socket lesen
     *
     * @access  public
     * @param   int size default 4096 Anzahl zu lesender Bytes
     * @return  string Gelesene Daten
     */
    function gets($size= 4096) {
      return fgets($this->_hSocket, $size);
    }
    
    /**
     * Eine Zeile vom Socket lesen. Schneidet \r und/oder \n am Ende des Strings ab
     *
     * @access  public
     * @param   int size default 4096 Anzahl zu lesender Bytes
     * @return  string Gelesene Daten
     */
    function readLine($size= 4096) {
      return chop($this->gets($size));
    }

    /**
     * Vom Socket lesen
     *
     * @access  public
     * @param   int size default 4096 Anzahl zu lesender Bytes
     * @return  string Gelesene Daten
     */
    function read($size= 4096) {
      return fread($this->_hSocket, $size);
    }
    
    /**
     * Testet auf EOF vom Socket
     *
     * @access  public
     * @return  bool EOF erhalten
     */
    function eof() {
      return feof($this->_hSocket);
    }
    
    /**
     * Schreibt auf den Socket
     *
     * @access  public
     * @param   string str Der zu schreibende String
     * @return  bool Ob's geklappt hat
     */    
    function write($str) {
      return fputs($this->_hSocket, $str);
    }

    /**
     * Socket Block-Modus setzen
     *
     * @access  public
     * @param   bool blockMode
     * @see     php://socket_set_blocking
     */
    function block($blockMode) {
      socket_set_blocking($this->_hSocket, $blockMode);
    }
    
    /**
     * Socket schließen
     *
     * @access  public
     */
    function close() {    
      if (!$this->isConnected()) return;
      @fclose($this->_hSocket);
      unset($this->_hSocket);
    }
    
    /**
     * Destructor
     */
    function __destruct() {
      $this->close();
      parent::__destruct();
    }
  }
?>
