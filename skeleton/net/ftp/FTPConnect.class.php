<?php
  import('net/NetException');
  
  class FTPConnect extends Object {
    var
      $host,
      $user,
      $pass,
      $port;
      
    var
      $_chdl;

    function FTPConnect($params= NULL) {
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      $this->port= 21;
      parent::__construct($params);
      $this->_chdl= NULL;
    }
        
    function connect() {
      if ($this->_chdl != NULL) return 0;       // already connected
      $this->_chdl= @ftp_connect(
        $this->host,
        $this->port
      );
      if (!$this->_chdl) return throw(
        E_NET_CONNECT_EXCEPTION,
        "couldn't connect to {$this->host}:{$this->port}"
      );
      return $this->_chdl;
    }
    
    function login() {
      if ($this->_chdl == NULL && !$this->connect()) return 0;
      if (!ftp_login($this->_chdl, $this->user, $this->pass)) return throw(
        E_NET_LOGIN_EXCEPTION,
        "couldn't login to {$this->host} as {$this->user} [using password '{$this->pass}']"
      );
      return 1;
    }
    
    function setDir($dir) {
      return @ftp_chdir($this->_chdl, $dir);
    }
    
    function getDir($dir) {
      return @ftp_pwd($this->_chdl);
    }
    
    function disconnect() {
      if ($this->_chdl != NULL) return @ftp_quit($this->_chdl);
    }

    function __destruct() {
      $this->disconnect();
      parent::__destruct();
    }
  }
?>
