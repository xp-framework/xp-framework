<?php
  define('SOAP_REQUEST_SYNC',   'Sync');
  define('SOAP_REQUEST_ASYNC',  'ASync');

  import('xml/soap/SOAPEnvelope');
  
  class SOAPRequestFactory {
    var $type;
    
    function SOAPRequestFactory($type, $params= NULL) {
      $this->type= $type;
      $type= "SOAP{$this->type}Request";
      import('xml/soap/'.$type);
      $this= new $type($params);
    }
  }
?>
