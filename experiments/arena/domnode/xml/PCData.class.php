<?php
/* This class is part of the XP framework
 *
 * $Id: PCData.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  /**
   * PCData allows to insert literal XML into a nodes contents.
   *
   * Example:
   * <code>
   *   $tree= &new Tree();
   *   $tree->addChild(new Node('data', new PCData('Hello<br/>World')));
   * </code>
   *
   * The output will then be:
   * <pre>
   *   <document>
   *     <data>Hello<br/>World</data>
   *   </document>
   * </pre>
   *
   * Note: The XML passed to PCDatas constructor is not validated!
   * Passing incorrect XML to this class will result in a not-
   * wellformed output document.
   *
   * @purpose  Wrapper
   */
  class PCData extends Object {
    public
      $pcdata= '';
      
    /**
     * Constructor
     *
     * @param   string pcdata
     */
    public function __construct($pcdata) {
      $this->pcdata= $pcdata;
    }
    
    /**
     * Retrieve XML representation as DOM element
     *
     * @param   php.DOMDocument doc
     * @return  php.DOMElement
     */
    public function getDomNode(DOMDocument $doc) {
      $tdoc= new DOMDocument();
      $tdoc->loadXML('<?xml version="1.0" encoding="iso-8859-1"?><t>'.$this->pcdata.'</t>');
      
      $fragment= $doc->createDocumentFragment();
      foreach ($tdoc->documentElement->childNodes as $node) {
        $fragment->appendChild($doc->importNode($node, TRUE));
      }
      
      return $fragment;
    }
  }
?>
