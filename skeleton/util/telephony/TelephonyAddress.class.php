<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  define('TEL_ADDRESS_EXTERNAL',         'ext');
  define('TEL_ADDRESS_INTERNAL',         'int');
  define('TEL_ADDRESS_INTERNATIONAL',    'itl');

  /**
   * Represents an address
   *
   * @purpose  an abstract wrapper for addresses
   */
  class TelephonyAddress extends Object {
    var
      $type     = TEL_ADDRESS_INTERNAL,
      $number   = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str a phone number int the type:number notation
     */
    function __construct($str) {
      list($this->type, $this->number)= explode(':', $str, 2);
      parent::__construct();
    }
    
    /**
     * Retreive the phone number
     *
     * @access  public
     * @return  string number
     */
    function getNumber() {
      return $this->number;
    }
    
    /**
     * Retreive the type
     *
     * @access  public
     * @return  string type one of the TEL_ADDRESS_* constants
     */
    function getType() {
      return $this->type;
    }
  }
?>
