<?php
  /* Synchroner SOAP-Request per HTTP - Anforderung
   *
   * $Id$
   */
   
  import('net.http.HTTPRequest');

  class SOAPSyncRequest extends XML {
    var $envelope;
    
    var
      $host,
      $port,
      $timeout,
      $handler,
      $auth;
    
    var
      $_response;
      
    function SOAPSyncRequest($params= NULL) {
      $this->envelope= NULL;
      $this->host= NULL;
      $this->port= 80;
      $this->timeout= 10;
      $this->handler= '/';
      parent::__construct($params);
    }
    
    function setAuth($username= NULL, $password= NULL) {
      if (NULL== $username && NULL== $password) {
        unset($this->auth);
      } else {
        $this->auth= array($username, $password);
      }
    }
    
    function send() {
      if (NULL== $this->envelope) return throw(
        E_PARAM_EXCEPTION, 'envelope is not a valid SOAP envelope'
      );
      if (NULL== $this->host) return throw(
        E_PARAM_EXCEPTION, 'host is empty'
      );    
      
      // POST
      $req= new HTTPRequest(array(
        'host'    => $this->host,
        'port'    => $this->port,
        'target'  => $this->handler,
        'timeout' => $this->timeout
      ));
      
      // Muss authentifiziert werden?
      if (isset($this->auth)) {
        list($req->authUser, $req->authPassword)= $this->auth;
      }
            
      if (!$req->post(array(
        'SOAPXML' => urlencode($this->envelope->getSource())
      ))) return 0;
      
      $this->_response= $req->response;
      return 1;
    }
    
    function getResponse() {
      if (!isset($this->_response)) return throw(
        E_ILLEGAL_STATE_EXCEPTION,
        'no response (yet)'
      );
      return $this->_response;
    }
  }
?>
