<?php
  /**
   * Repräsetiert benamte Element
   */
  class SOAPNamedItem extends Object {
    var
      $name= 'item',
      $type= 'string',
      $val;
      
    function __construct($name, $val, $type= NULL) {
      $this->name= $name;
      $this->val= $val;
      $this->type= (NULL == $type) ? gettype($val) : $type;
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
      return $this->type;
    }
    
    function getItemName() {
      return $this->name;
    }

  }
?>
