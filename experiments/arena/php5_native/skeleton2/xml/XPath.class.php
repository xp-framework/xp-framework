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
  class XPath extends Object {
    
    /**
     * Constructor. Accepts  the following types as argument:
     * <ul>
     *   <li>A string containing the XML</li>
     *   <li>A DomDocument object (as returned by domxml_open_mem, e.g.)</li>
     *   <li>An xml.Tree object</li>
     * </ul>
     *
     * @access  public
     * @param   mixed arg
     * @throws  lang.IllegalArgumentException
     * @throws  xml.XMLFormatException in case the argument is a string and not valid XML
     */
    public function __construct($arg) {
      switch (xp::typeOf($arg)) {
        case 'string':
          if (!($dom= &domxml_open_mem($arg, DOMXML_LOAD_PARSING, $error))) {
            throw(new XMLFormatException(
              rtrim($error[0]['errormessage']), 
              XML_ERROR_SYNTAX, 
              NULL,
              $error[0]['line'], 
              $error[0]['col']
            ));
          }
          $this->context= &xpath_new_context($dom);
          break;
        
        case 'php.domdocument':
          $this->context= &xpath_new_context($arg);
          break;
        
        case 'xml.Tree':
          $this->context= &xpath_new_context(domxml_open_mem($arg->getSource()));
          break;
        
        default:
          throw(new IllegalArgumentException('Unsupported parameter type '.xp::typeOf($arg)));
      }
    }
    
    /**
     * Execute xpath query and return results
     *
     * @access  public
     * @param   string xpath
     * @param   php.DomNode node default NULL
     * @return  php.XPathObject
     * @throws  xml.XPathException if evaluation fails
     */
    public function query($xpath, $node= NULL) {
      if ($node) {
        $r= &xpath_eval($this->context, $xpath, $node);
      } else {
        $r= &xpath_eval($this->context, $xpath);
      }
      if (FALSE === $r) {
        throw(new XPathException('Cannot evaluate "'.$xpath.'"'));
      }
      return $r;
    }
  }
?>
