<?php
  /**
   * Repräsetiert SOAP-Base64-encodeten String
   *
   */
  class SOAPBase64Binary extends Object {
    var $string;
    
    /**
     + Constructor
     */
    function __construct($string) {
      $this->string= $string;
      Object::__construct();
    }
    
    /**
     * Gibt den String
     *
     * @access  public
     * @return  string Base64-encodeter String
     */
    function toString() {
      return base64_encode($this->string);
    }
    
    /**
     * Typ-Name
     *
     * @access  public
     * @return  string Typ-Namen
     */
    function getType() {
      return 'base64Binary';
    }
  }
?>
