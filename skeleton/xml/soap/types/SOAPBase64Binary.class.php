<?php
  /**
   * Repräsetiert SOAP-Base64-encodeten String
   *
   */
  class SOAPBase64Binary extends Object {
    var
      $string,
      $encoded;
    
    /**
     + Constructor
     */
    function __construct($string, $is_encoded= FALSE) {
      $this->fromString($string, $is_encoded);
      parent::__construct();
    }
    
    /**
     * Gibt den String-Wert zurück
     *
     * @access  public
     * @return  string Base64-encodeter String
     */
    function toString() {
      return $this->encoded;
    }
    
    /**
     * Setzt den String
     *
     * @access  public
     * @param   string str Der String
     */
    function fromString($str, $is_encoded= FALSE) {
      if ($is_encoded) {
        $this->string= base64_decode($str);
        $this->encoded= $str;
      } else {
        $this->string= $str;
        $this->encoded= base64_encode($str);
      }
    }
    
    /**
     * Typ-Name
     *
     * @access  public
     * @return  string Typ-Namen
     */
    function getType() {
      return 'xsd:base64Binary';
    }
    
    function getItemName() {
      return FALSE;
    }
  }
?>
