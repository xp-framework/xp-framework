<?php
  class SOAPFault extends XML {
    var 
      $faultcode, 
      $faultstring, 
      $faultactor= NULL;
      
    function create($faultcode, $faultstring, $faultactor= NULL) {
      $this->faultcode= $faultcode;
      $this->faultstring= $faultstring;
      $this->faultactor= $faultactor;
    }
    
  }
?>
