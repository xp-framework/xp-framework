<?php
  import('net/NetException');
  
  class Socket extends Object {
    var
      $host,
      $port,
      $timeout;
  
    var
      $_hSocket;
      
    function Socket($params= NULL) {
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      // Default timeout: 2 seconds
      $this->timeout= 2;
      
      parent::__construct($params);
    }
    
    function isConnected() {
      return isset($this->_hSocket) ? 1 : 0;
    }
    
    function connect() {
      if ($this->isConnected()) return throw(
        E_ILLEGAL_STATE_EXCEPTION,
        'sock::socket already open'
      );
      if (empty($this->host)) return throw(
        E_ILLEGAL_ARGUMENT_EXCEPTION,
        'sock::no host specified'
      );
      if (!$this->_hSocket= fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout)) return throw(
        E_NET_CONNECT_EXCEPTION, sprintf(
          'sock::failed connecting to %s:%s within %s seconds [%d: %s]',
          $this->host,
          $this->port,
          $this->timeout,
          $errno,
          $errstr
        )
      );
      return 1;
    }
    
    function read($size= 4096) {
      return fgets($this->_hSocket, $size);
    }
    
    function eof() {
      return feof($this->_hSocket);
    }
    
    function write($str) {
      return fputs($this->_hSocket, $str);
    }
    
    function block($blockMode) {
      socket_set_blocking($this->_hSocket, $blockMode);
    }
    
    function close() {    
      if (!$this->isConnected()) return;
      @fclose($this->_hSocket);
      unset($this->_hSocket);
    }
    
    function __destruct() {
      $this->close();
      parent::__destruct();
    }
  }
?>
