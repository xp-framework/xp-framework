<?php
  /**
   * Repräsetiert benamte Element
   */
  class SOAPNamedItem extends Object {
    var
      $name= 'item',
      $val;
      
    function __construct($name, $val) {
      $this->name= $name;
      $this->val= $val;
      parent::__construct();
    }
    
    function toString() {
      return $this->val;
    }
    
    /**
     * Typ-Name
     *
     * @access  public
     * @return  string Typ-Namen
     */
    function getType() {
      return NULL;
    }
    
    function getItemName() {
      return $this->name;
    }

  }
?>
