<?php
  class SOAPFault extends XML {
    var 
      $faultcode, 
      $faultstring, 
      $faultactor= NULL,
      $detail= NULL;
      
    function create($faultcode, $faultstring, $faultactor= NULL, $detail= NULL) {
      $this->faultcode= $faultcode;
      $this->faultstring= $faultstring;
      $this->faultactor= $faultactor;
      $this->detail= $detail;
    }
    
  }
?>
