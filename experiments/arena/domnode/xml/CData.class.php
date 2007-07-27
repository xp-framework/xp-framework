<?php
/* This class is part of the XP framework
 *
 * $Id: CData.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  /**
   * CData allows to insert a CDATA section:
   *
   * Example:
   * <code>
   *   $tree= &new Tree();
   *   $tree->addChild(new Node('data', new CData('<Hello World>')));
   * </code>
   *
   * The output will then be:
   * <pre>
   *   <document>
   *     <data><![CDATA[<Hello World>]]></data>
   *   </document>
   * </pre>
   *
   * @purpose  Wrapper
   */
  class CData extends Object {
    public
      $cdata= '';
      
    /**
     * Constructor
     *
     * @param   string cdata
     */
    public function __construct($cdata) {
      $this->cdata= $cdata;
    }
    
    /**
     * Retrieve XML representation as DOM element
     *
     * @param   php.DOMDocument doc
     * @return  php.DOMElement
     */
    public function getDomNode(DOMDocument $doc) {
      return $doc->createCDataSection($this->cdata);
    }
  }
?>
