<?php
  define('HTTP_METHOD_GET',     'GET');
  define('HTTP_METHOD_POST',    'POST');
  define('HTTP_METHOD_HEAD',    'HEAD');
  
  uses(
    'net.Socket',
    'net.http.HTTPAuthException',
    'io.IOException'
  );
  
  class HTTPRequest extends Socket {
    var 
      $method,
      $request,
      $target,
      $response;
    
    var
      $contentType= 'application/x-www-form-urlencoded';
    
    var
      $authType,
      $authUser,
      $authPassword;

    function HTTPRequest($params= NULL) {
      if (isset($params['url'])) {
        $p= parse_url($params['url']);
        $params['host']= $p['host'];
        $params['port']= isset($p['port']) ? $p['port'] : 80;
        $params['target']= $p['path'].@$p['query'];
      }
      $this->__construct($params);
    }
    
    function __construct($params= NULL) {
      $this->port= 80;
      Socket::__construct($params);
    }
        
    function _request($vars) {
      if (!$this->isConnected()) {
        try(); {
          $this->connect();
        } if (catch('Exception', $e)) {
          $e->message= 'HTTPRequest::_request()==>'.$e->message;
          return throw($e);
        }
      }
      
      if (is_array($vars)) {
        $data= '';
        foreach ($vars as $key=> $val) {
          $data.= '&'.$key.'='.urlencode($val);
        }
      } else {
        $data= $vars;
      }
      
      switch ($this->method) {
        case HTTP_METHOD_POST: 
          $body= substr($data, 1)."\n";
          break;
          
        default:
          $body= '';
          if ('' != $data) $this->target.= '?'.substr($data, 1);
      }

      // Request absenden
      $this->request= sprintf(
        "%s %s HTTP/1.1\r\nHost: %s\r\nContent-Type: %s\r\n%sConnection: close\r\n",
        $this->method,
        $this->target,
        $this->host,
        $this->contentType,
        ($this->method== HTTP_METHOD_POST ? 'Content-Length: '.strlen($data)."\r\n" : '')
      );
      if (!empty($this->headers)) {
        foreach ($this->headers as $key=> $val) {
          $this->request.= $key.': '.$val."\r\n";
        }
      }
      
      // Auth?
      if (isset($this->authUser)) {
        $this->request.= sprintf(
          "Authorization: %s %s\r\n",
          (isset($this->authType) ? $this->authType : 'Basic'),
          base64_encode($this->authUser.':'.$this->authPassword)
        );
      }
      
      //DEBUG
      //echo($this->request);
      //flush();
      
      // Absenden
      $this->request.= "\r\n".$body;
      try(); {
        $this->write($this->request);
      } if (catch('Exception', $e)) {
        return throw(new IOException($e->getStackTrace().'::'.$this->request));
      }

      // Antwort lesen
      $this->response= new StdClass();
      $this->response->body= '';
      $this->response->TransferEncoding= '';
      $header= TRUE;
      $footer= FALSE;
      while(!$this->eof()) {
        $answer= $this->read();
        // echo("ANSWER |".urlencode($answer).'|HEADER='.($header ? 'TRUE' : 'FALSE').'|FOOTER='.($footer ? 'TRUE' : 'FALSE')."|\n");
        
        if (''== trim(chop($answer)) && $header) {
          $header= FALSE;
        }
        
        // Header? Footer? Body?
        if (!$header && !$footer) {
        
          // Chunked?
          if (stristr($this->response->TransferEncoding, 'chunked')) {
            //echo 'CHUNK |'.urlencode($answer).'|LAST='.urlencode(substr($this->response->body, -2))."|\n";
            if ("\r\n" == substr($this->response->body, -2) && preg_match('/^([0-9a-fA-F]+)(( ;.*)| )?\r\n$/', $answer, $regs)) {
            
              // Last Chunk?
              if ('0' == $regs[1]) {
                //echo "LASTCHUNK: |".$regs[1]."|\n";
                $footer= TRUE;
              }
              continue;
            }
          }
          
          //echo "BODY+=".urlencode($answer)."\n";
          $this->response->body.= $answer;
          continue;
        }
        
        // HTTP-Status: HTTP/1.1 200 OK
        if (preg_match('|^HTTP/[0-9.]+ ([0-9]+) ?([^\r\n]*)|', $answer, $regs)) {
          $this->response->HTTPstatus= $regs[1];
          $this->response->HTTPmessage= $regs[2];
          continue;
        }
        
        // Andere HTTP-Response-Codes
        if (!strstr($answer, ':')) continue;
        //echo "HEADER+=".urlencode($answer)."\n";
        list($key, $val)= explode(': ', $answer);
        if ('Location' == $key) $this->response->HTTPredirect= parse_url(trim(chop($val)));
        $this->response->{str_replace('-', '', $key)}= trim(chop($val));
      }
      $this->response->body= substr($this->response->body, 2);
      
      // Fehler auswerten
      $result= 0;
      switch($this->response->HTTPstatus) {
        case 401: 
          throw(new HTTPAuthException($this->response->HTTPmessage));
          break;
        default:
          $result= 1;
      }
      
      // Verbindung schließen
      $this->close();
      return $result;
    }
    
    function post($postVars= '') {
      $this->method= HTTP_METHOD_POST;
      return $this->_request($postVars);
    }
    
    function get($getVars= '') {
      $this->method= HTTP_METHOD_GET;
      return $this->_request($getVars);
    }

    function head($headVars= '') {
      $this->method= HTTP_METHOD_HEAD;
      return $this->_request($headVars);
    }

    function __destruct() {
      parent::__destruct();
    }
  }
?>
