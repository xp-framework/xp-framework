<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.XPathException', 'xml.XMLFormatException');

  /**
   * XPath class
   *
   * <code>
   *   uses('xml.XPath');
   * 
   *   $xml= '<dialog id="file.open">
   *    <caption>Open a file</caption>
   *      <buttons>
   *        <button name="ok"/>
   *        <button name="cancel"/>
   *      </buttons>
   *   </dialog>';
   *   
   *   echo create(new XPath($xml))->query('/dialog/buttons/button/@name'));
   * </code>
   *
   * @ext      dom
   * @test     xp://net.xp_framework.unittest.xml.XPathTest
   * @purpose  Provide XPath functionality
   */
  class XPath extends Object {
    public $context= NULL;

    static function __static() {
      libxml_use_internal_errors(TRUE);
    }
    
    /**
     * Helper method
     *
     * @param   string xml
     * @return  php.DOMDocument
     * @throws  xml.XMLFormatException if the given XML is not well-formed or unparseable
     */
    protected function loadXML($xml) {
      $doc= new DOMDocument();
      if (!$doc->loadXML($xml)) {
        $e= new XMLFormatException('Cannot load XML');    // TODO: Capture error details
        xp::gc(__FILE__);
        throw $e;
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
     * @param   var arg
     * @throws  lang.IllegalArgumentException
     * @throws  xml.XMLFormatException in case the argument is a string and not valid XML
     */
    public function __construct($arg) {
      if ($arg instanceof DOMDocument) {
        $this->context= new DOMXPath($arg);
      } else if ($arg instanceof Tree) {
        $this->context= new DOMXPath($this->loadXML(
          $arg->getDeclaration().$arg->getSource(INDENT_NONE)
        ));
      } else if (is_string($arg)) {
        $this->context= new DOMXPath($this->loadXML($arg));
      } else {
        throw new IllegalArgumentException('Unsupported parameter type '.xp::typeOf($arg));
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
    public function query($xpath, $node= NULL) {
      if ($node) {
        $r= $this->context->evaluate($xpath, $node);
      } else {
        $r= $this->context->evaluate($xpath);
      }
      if (FALSE === $r) {
        throw new XPathException('Cannot evaluate "'.$xpath.'"');
      }
      return $r;
    }
  }
?>
