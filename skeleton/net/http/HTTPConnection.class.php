<?php
  define('HTTP_METHOD_GET',     'GET');
  define('HTTP_METHOD_POST',    'POST');
  define('HTTP_METHOD_HEAD',    'HEAD');
  
  define('TRANSFER_ENCODING_NORMAL',  0x0000);
  define('TRANSFER_ENCODING_CHUNKED', 0x0001);
  
  uses(
    'net.Socket',
    'net.http.HTTPAuthException',
    'io.IOException'
  );
  
  class HTTPConnection extends Socket {
    var 
      $method,
      $request,
      $target,
      $response;
      
    var
      $port        = 80,
      $contentType = 'application/x-www-form-urlencoded';
    
    var
      $authType,
      $authUser,
      $authPassword;

    function __construct($params= NULL) {
      if (is_string($params)) {
        $p= parse_url($params);
        $params= array(
          'host'        => $p['host'],
          'port'        => isset($p['port']) ? $p['port'] : 80,
          'target'      => $p['path'].@$p['query']
        );
      }
      parent::__construct($params);
    }
    
    function _request($vars) {
      if (!$this->isConnected()) {
        try(); {
          $this->connect();
        } if (catch('Exception', $e)) {
          $e->message= 'HTTPConnection::_request()==>'.$e->message;
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
      
      $target= $this->target;
      switch ($this->method) {
        case HTTP_METHOD_POST: 
          $body= substr($data, 1)."\n";
          break;
          
        default:
          $body= '';
          if ('' != $data) $target.= '?'.substr($data, 1);
      }

      // Request absenden
      $this->request= sprintf(
        "%s %s HTTP/1.1\r\nHost: %s\r\nContent-Type: %s\r\n%sConnection: close\r\n",
        $this->method,
        $target,
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
      if (!$this->write($this->request)) return throw(new IOException($this->request));

      // Antwort lesen
      $this->response= new StdClass();
      $this->response->status= 0;
      $this->response->message= '';
      $this->response->location= NULL;
      $this->response->encoding= TRANSFER_ENCODING_NORMAL;
      $this->response->header= array();
      $this->response->footer= array();
      $this->response->body= '';
      
      while (!$this->eof()) {
        $answer= $this->read();
        // echo("ANSWER |".urlencode($answer).'|HEADER='.($header ? 'TRUE' : 'FALSE').'|FOOTER='.($footer ? 'TRUE' : 'FALSE')."|\n");
        
        // Die erste leere Zeile trennt Header und Body
        if (''== trim(chop($answer))) {
          break;
        }
        
        // HTTP-Status: HTTP/1.1 200 OK
        if (preg_match('|^HTTP/[0-9.]+ ([0-9]+) ?([^\r\n]*)|', $answer, $regs)) {
          $this->response->status= $regs[1];
          $this->response->message= $regs[2];
          
          // HTTP/1.1 100 Continue: Read and discard
          // http://lists.w3.org/Archives/Public/www-talk/1996SepOct/0078.html
          // http://www.w3.org/Protocols/rfc2616/rfc2616-sec8.html#sec8.2.3
          if (100 == $regs[1]) do {
            $answer= $this->read();
          } while ('' != trim(chop($answer)));
          continue;
        }
        
        
        
        // Andere HTTP-Response-Codes
        if (!strstr($answer, ':')) continue;
        
        //echo "HEADER+=".urlencode($answer)."\n";
        list($key, $val)= explode(': ', $answer);
        $this->response->header[$key]= trim(chop($val));
        
        // Special
        if (!strcasecmp('Location', $key)) {
          $this->response->location= parse_url(trim(chop($val)));
        }
        
        if (!strcasecmp('Transfer-Encoding', $key)) {
          $this->response->encoding= stristr($val, 'chunked') 
            ? TRANSFER_ENCODING_CHUNKED 
            : TRANSFER_ENCODING_NORMAL;
        }
      }
      
      // Fehler auswerten
      $result= 0;
      switch($this->response->HTTPstatus) {
        case 401: 
          throw(new HTTPAuthException($this->response->HTTPmessage));
          break;
        default:
          $result= 1;
      }
      
      return $result;
    }
    
    function getResponse() {
      if ($this->eof()) {
        $this->close();
        return FALSE;
      }
      
      $answer= $this->read();

      // Chunked?
      if (
        (TRANSFER_ENCODING_CHUNKED == $this->response->encoding) &&
        (preg_match('/^([0-9a-fA-F]+)(( ;.*)| )?\r\n$/', $answer, $regs))
      ) {
        printf("---> CHUNKED %s [%s]\n", urlencode($answer), chop($regs[0]));
        return $this->getResponse();
      }

      $this->response->body.= $answer;
      return $answer;
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
