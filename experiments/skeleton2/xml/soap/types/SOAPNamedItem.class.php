<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.soap.types.SoapType');

  /**
   * SOAP named items. Per default, SOAP values do not have names.
   * Some applications do not depend on the order the values are
   * passed in as but rather look for symbolic names. IMHO, this
   * violates the rule of "RPC" (remote _procedure_ calls), that is,
   * calling procedures (known to PHP as functions) remotely. In no
   * language I know the _name_ of a parameter matters, e.g.
   * <code> 
   *   function foo($arg) { }
   *   
   *   $a= 1;
   *   $b= 1;
   *   foo($a);
   *   foo($b);
   * </code>
   * will yield the same result (of course), no matter what _name_ the
   * variable passed to foo() has. It is absolutely impossible from
   * within foo() to find out the name of the variable passed (as, of
   * course, this is of no difference!). But, alas, other people do 
   * not think so, and so, there's a NamedItem.
   *
   * @see      xp://xml.soap.types.SoapType
   * @purpose  DateTime type
   */
  class SOAPNamedItem extends SoapType {
    public
      $name  = 'item',
      $value = NULL;
     
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed val
     */ 
    public function __construct($name, $val) {
      $this->name= $name;
      $this->value= $val;
      parent::__construct();
    }
    
    /**
     * Return a string representation for use in SOAP
     *
     * @access  public
     * @return  mixed
     */
    public function toString() {
      return $this->value;
    }
    
    /**
     * Returns this type's name
     *
     * @access  public
     * @return  string
     */
    public function getType() {
      return NULL;
    }
    
    /**
     * Returns this type's name or FALSE if there's no 
     * special name
     *
     * @access  public
     * @return  string
     */    
    public function getItemName() {
      return $this->name;
    }

  }
?>
