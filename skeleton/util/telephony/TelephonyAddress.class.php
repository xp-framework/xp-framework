<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  define('TEL_CALL_INTERNATIONAL',      0x0000);
  define('TEL_CALL_NATIONAL',           0x0004);
  define('TEL_CALL_CITY',               0x0006);
  define('TEL_CALL_INTERNAL',           0x0007);

  /**
   * Represents an address
   *
   * @purpose  an abstract wrapper for addresses
   */
  class TelephonyAddress extends Object {
    var
      $type     = TEL_ADDRESS_INTERNAL;
      
    var
      $countryCode,
      $areaCode,
      $subscriber,
      $ext;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str a phone number int the type:number notation
     */
    function __construct() {
      parent::__construct();
    }
    
    /**
     * Retreive the phone number in nice human readable form.
     *
     * @access  public
     * @param int callCategory for which "view" do we need the number
     * @return string number
     */
    function toString($category= TEL_CALL_INTERNATIONAL) {
      $display= array (
        TEL_CALL_INTERNATIONAL => '%s %s %s%s',
        TEL_CALL_NATIONAL      => '0%2$s %3$s%4$s',
        TEL_CALL_CITY          => '%3$s%4$s',
        TEL_CALL_INTERNAL      => '%4$s'
      );
      
      return sprintf ($display[$category],
        $this->getCountryCode(),
        $this->areaCode,
        $this->getSubscriber().(strlen ($this->getExt()) ? '-' : ''),
        $this->getExt()
      );
    }
    
    /**
     * Get a rather technically based view for the address. That is
     * do not print any separators like space or minus.
     *
     * This form is not good to be parsed again, but to be given
     * to an real phone system.
     *
     * @access public
     * @param int callCategory
     * @return string number
     */    
    function getNumber($category= TEL_CALL_INTERNATIONAL) {
      $display= array (
        TEL_CALL_INTERNATIONAL => '%s%s%s%s',
        TEL_CALL_NATIONAL      => '0%2$s%3$s%4$s',
        TEL_CALL_CITY          => '%3$s%4$s',
        TEL_CALL_INTERNAL      => '%4$s'
      );
      
      return sprintf ($display[$category],
        $this->getCountryCode(),
        $this->areaCode,
        $this->getSubscriber(),
        $this->getExt()
      );
    }

    /**
     * Set the type of phone number
     *
     * @access public
     * @param string type
     */    
    function setType($type= TEL_ADDRESS_INTERNATIONAL) {
      $this->type= $type;
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
    
    /**
     * Sets the country code for this address
     *
     * @access public
     * @param string cc country code to set
     * @throws IllegalArgumentException if country code is not 
     *         in expected form or malformed
     */    
    function setCountryCode($cc) {
      if ('+' != $cc{0} || 2 > strlen ($cc))
        return throw (new IllegalArgumentException ('Malformed country code: '.$cc));

      $this->countryCode= substr ($cc, 1);
    }

    /**
     * Get the country code
     *
     * @access public
     * @return string country code 
     * @throw IllegalStateException if cc not set
     */    
    function getCountryCode() {
      if (empty ($this->countryCode))
        return throw (new IllegalStateException ('Country code not yet initialized'));
    
      return '+'.$this->countryCode;
    }
    
    /**
     * Sets the area code for this address
     *
     * @access public
     * @param string areaCode area code to set
     * @throws IllegalArgumentException if area code is malformed
     */
    function setAreaCode($areaCode) {
      if ('0' != $areaCode{0} || 2 > strlen ($areaCode))
        return throw (new IllegalArgumentException ('Malformed area code: '.$areaCode));

      $this->areaCode= substr ($areaCode, 1);
    }
    
    /**
     * Get the area code
     *
     * @access public
     * @return string area code
     * @throws IllegalStateException if areacode is not set
     */    
    function getAreaCode() {
      if (empty ($this->areaCode))
        return throw (new IllegalStateException ('Areacode not yet initialized'));
    
      return '0'.$this->areaCode;
    }
    
    /**
     * Sets the subscriber part
     *
     * @access public
     * @param string subscriber 
     */
    function setSubscriber($subscriber) {
      // TODO: Syntaxchecking
      $this->subscriber= $subscriber;
    }

    /**
     * Get the subscriber part
     *
     * @access public
     * @return string subscriber
     * @throws IllegalStateException if areacode is not set
     */    
    function getSubscriber() {
      if (empty ($this->subscriber))
        return throw (new IllegalStateException ('Subscriber not yet initialized'));
        
      return $this->subscriber;
    }
    
    /**
     * Sets the extension part
     *
     * @access public
     * @param string ext
     */
    function setExt($ext) {
      // TODO: Syntaxchecking
      $this->ext= $ext;
    }
    
    /**
     * Get extension if any is available
     *
     * @access public
     * @return string ext
     */    
    function getExt() {
      return $this->ext;
    }
    
    /**
     * Returns the category for a call of $this number to the remote number
     *
     * @access public
     * @param &TelephonyAddress remoteAddress
     * @return int callcategory
     */    
    function getCallCategory(&$r) {
      if ($this->countryCode != $r->countryCode)
        return TEL_CALL_INTERNATIONAL;
    
      if ($this->areaCode != $r->areaCode)
        return TEL_CALL_NATIONAL;
    
      if ($this->subscriber != $r->subscriber)
        return TEL_CALL_CITY;
      
      return TEL_CALL_INTERNAL;
    }
    
  }
?>
