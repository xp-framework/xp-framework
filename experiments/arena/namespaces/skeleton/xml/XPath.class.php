<?php
/* This class is part of the XP framework
 *
 * $Id: XPath.class.php 9982 2007-04-13 15:15:44Z friebe $
 */

  namespace xml;

  uses('xml.XPathException', 'xml.XMLFormatException');

  /**
   * XPath class
   *
   * <code>
   *   uses('xml.XPath');
   * 
   *   $xml= <<<__
   * <dialog id="file.open">
   *   <caption>Open a file</caption>
   *   <buttons>
   *     <button name="ok"/>
   *     <button name="cancel"/>
   *   </buttons>
   * </dialog>
   * __;
   *   
   *   $xpath= &new XPath($xml);
   *   var_dump($xpath->query('/dialog/buttons/button/@name'));
   * </code>
   *
   * @ext      domxml
   * @purpose  Provide XPath functionality
   */
  class XPath extends lang::Object {
    public
      $context= NULL;

    /**
     * Helper method
     *
     * @param   string xml
     * @return  php.DOMDocument
     */
    protected function loadXML($xml) {
      try {
        $doc= new ::DOMDocument();
        $doc->loadXML($xml);
      } catch (::DOMException $e) {
        throw new XMLFormatException($e->getMessage());
      }
      
      return $doc;
    }
    
    /**
     * Constructor. Accepts  the following types as argument:
     * <ul>
     *   <li>A string containing the XML</li>
     *   <li>A DomDocument object (as returned by domxml_open_mem, e.g.)</li>
     *   <li>An xml.Tree object</li>
     * </ul>
     *
     * @param   mixed arg
     * @throws  lang.IllegalArgumentException
     * @throws  xml.XMLFormatException in case the argument is a string and not valid XML
     */
    public function __construct($arg) {
      switch (::xp::typeOf($arg)) {
        case 'string':
          $this->context= new ($this->loadXML($arg));
          break;
        
        case 'php.DOMDocument':
          $this->context= new ($arg);
          break;
        
        case 'xml.Tree':
          $this->context= new ($this->loadXML($arg->getSource()));
          break;
        
        default:
          throw(new lang::IllegalArgumentException('Unsupported parameter type '.::xp::typeOf($arg)));
      }
    }
    
    /**
     * Execute xpath query and return results
     *
     * @param   string xpath
     * @param   php.DomNode node default NULL
     * @return  php.DOMNodeList
     * @throws  xml.XPathException if evaluation fails
     */
    public function query($xpath, $node= ) {
      if ($node) {
        $r= $this->context->evaluate($xpath, $node);
      } else {
        $r= $this->context->evaluate($xpath);
      }
      if (FALSE === $r) {
        throw(new XPathException('Cannot evaluate "'.$xpath.'"'));
      }
      return $r;
    }
  }
?>
