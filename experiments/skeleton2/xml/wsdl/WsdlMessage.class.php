<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Messages consist of one or more logical parts. Each part is associated 
   * with a type from some type system using a message-typing attribute.
   *
   * And example message's XML representation:
   * <xmp>
   *   <message name="doGoogleSearch">
   *     <part name="key"            type="xsd:string"/>
   *     <part name="start"          type="xsd:int"/>
   *     <part name="safeSearch"     type="xsd:boolean"/>
   *   </message>
   * </xmp>
   *
   * @see http://www.w3.org/TR/wsdl#_messages
   * @see xml.soap.wsdl.WsdlDocument#addMessage
   * @experimental
   */
  class WsdlMessage extends Object {
    public 
      $name=  '',
      $parts= array();
      
    /**
     * Constructor
     *
     * The argument parts is an associative array, where the keys
     * define the part names and the values might be one of the following:
     *
     * <ul>
     *   <li>a scalar describing the type</li>
     *   <li>an array of two values, the first being the type, the second the namespace</li>
     *   <li>an array of three values, 1st and 2nd as above, the third referencing an element</li>
     * </ul>
     *
     * Example:
     * <code>
     *   $d->addMessage(new WsdlMessage('doGoogleSearch', array(
     *     'key'        => 'string',
     *     'start'      => 'int',
     *     'safeSearch' => 'boolean'
     *   )));
     * </code>
     *
     * @access  public
     * @param   string name the message's name
     * @param   array parts default array() 
     */
    public function __construct($name, $parts= array()) {
      $this->name= $name;
      foreach ($parts as $k => $v) {
        if (is_scalar($v)) $v= array($v);
        array_unshift($v, $k);
        call_user_func_array(array(&$this, 'addPart'), $v);
      }
      
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Add a part
     *
     * @access  public
     * @param   string name
     * @param   string type
     * @param   string namespace default 'xsd'
     * @param   string element default NULL
     */
    public function addPart($name, $type, $namespace= 'xsd', $element= NULL) {
      $this->parts[$name]= new stdClass();
      $this->parts[$name]->type= $type;
      $this->parts[$name]->namespace= $namespace;
      $this->parts[$name]->element= $element;
    }
    
    /**
     * Get a part by its name. The returned object will have three
     * properties: type, namespace and element
     *
     * @access  public
     * @param   string name
     * @return  &object part
     */
    public function getPartByName($name) {
      if (isset($this->parts[$name])) return $this->parts[$name]; else return NULL;
    }
  }
?>
