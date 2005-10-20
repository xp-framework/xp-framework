<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represent a SOAP header element.
   *
   * @see       xp://xml.soap.SOAPMessage
   * @see       http://www.w3.org/TR/2000/NOTE-SOAP-20000508/#_Toc478383497
   * @purpose   Represent SOAP header element.
   */
  class SOAPHeaderElement extends Object {
    var
      $name=              '',
      $mustUnderstand=    FALSE,
      $actor=             '',
      $encodingStyle=     '',
      $value=             NULL;

    /**
     * Create a SOAPHeaderElement object out of the XML representation
     * it has in a SOAP message.
     *
     * @model   static
     * @access  public
     * @param   &xml.Node node
     * @param   &array ns current namespaces from SOAP message
     * @return  &xml.soap.SOAPHeaderElement
     */
    function &fromNode(&$node, $ns, $encoding) {
      $header= &new SOAPHeaderElement();
      $header->name= $node->getName();
      $header->mustUnderstand= (bool)$node->getAttribute($ns[XMLNS_SOAPENV].':mustUnderstand');
      $header->actor= $node->getAttribute($ns[XMLNS_SOAPENV].':actor');
      $header->encodingStyle= $node->getAttribute($ns[XMLNS_SOAPENV].':encodingStyle');
      $header->value= trim($node->getContent($encoding, $ns));
      
      return $header;
    }
    
    /**
     * Retrieve XML representation of this header for use in a SOAP
     * message.
     *
     * @access  public
     * @param   &array ns list of namespaces
     * @return  &xml.Node
     */
    function &getNode($ns) {
      $attr= array();
      if ($this->mustUnderstand) $attr[$ns[XMLNS_SOAPENV].':mustUnderstand']= 1;
      if ($this->actor) $attr[$ns[XMLNS_SOAPENV].':actor']= $this->actor;
      if ($this->encodingStyle) $attr[$ns[XMLNS_SOAPENV].':encodingStyle']= $this->encodingStyle;
      
      return new SOAPNode($this->name, $this->value, $attr);
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set MustUnderstand
     *
     * @access  public
     * @param   bool mustUnderstand
     */
    function setMustUnderstand($mustUnderstand) {
      $this->mustUnderstand= $mustUnderstand;
    }

    /**
     * Get MustUnderstand
     *
     * @access  public
     * @return  bool
     */
    function getMustUnderstand() {
      return $this->mustUnderstand;
    }

    /**
     * Set Actor
     *
     * @access  public
     * @param   string actor
     */
    function setActor($actor) {
      $this->actor= $actor;
    }

    /**
     * Get Actor
     *
     * @access  public
     * @return  string
     */
    function getActor() {
      return $this->actor;
    }

    /**
     * Set EncodingStyle
     *
     * @access  public
     * @param   string encodingStyle
     */
    function setEncodingStyle($encodingStyle) {
      $this->encodingStyle= $encodingStyle;
    }

    /**
     * Get EncodingStyle
     *
     * @access  public
     * @return  string
     */
    function getEncodingStyle() {
      return $this->encodingStyle;
    }

    /**
     * Set Value
     *
     * @access  public
     * @param   &lang.Object value
     */
    function setValue(&$value) {
      $this->value= &$value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getValue() {
      return $this->value;
    }

    /**
     * Build string representation of this header.
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= $this->getClassName().'@('.$this->hashCode()."){\n";
      $s.= '  [           name ] '.$this->name."\n";
      $s.= '  [          value ] '.$this->value."\n";
      $s.= '  [ mustUnderstand ] '.xp::stringOf($this->mustUnderstand)."\n";
      $s.= '  [  encodingStyle ] '.$this->encodingStyle."\n";
      $s.= '  [          actor ] '.$this->actor."\n";
      return $s.'}';
    }
    
  }
?>
