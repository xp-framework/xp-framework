<?php
/* This class is part of the XP framework
 *
 * $Id: OutputDocument.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace scriptlet::xml;   
  uses('xml.Tree', 'xml.Node');

  /**
   * Wraps outputdocument
   *
   * The XML document will look somewhat like this:
   * <xmp>
   *   <?xml version="1.0" encoding="iso-8859-1"?>
   *   <formresult                                                  
   *    serial="1034524928"                                         
   *    xmlns:xsd="http://www.w3.org/2001/XMLSchema"                
   *    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"       
   *   >                                                            
   *     <formvalues>                                               
   *       <param name="__page" xsi:type="xsd:string">home</param>  
   *     </formvalues>                                              
   *     <formerrors/>                                              
   *   </formresult>                                                
   * <xmp>
   *
   * These nodes are defined as following:
   * <pre>
   * - formresult
   *   Base node containing at least the two others and any other
   *
   * - formvalues
   *   Store any user-defined variables that need to be passed to
   *   the XSL stylesheet plus the request variables sent by the
   *   browser and their content
   *
   * - formerrors
   *   Contains all errors that have been caught by the scriptlet
   *   and are to be displayed within the web site (such as "wrong
   *   credentials supplied" on a login page)
   * </pre>
   *
   * @see xml.Tree
   */  
  class OutputDocument extends xml::Tree {
    public
      $formresult, 
      $formvalues, 
      $formerrors;

    /**
     * Constructor
     *
     */
    public function __construct() {
      parent::__construct();
      $this->formresult= new xml::Node('formresult', NULL, array(
        'serial'    => time(),
        'tz'        => date('Z'),
        'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema', 
        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
      ));
      $this->root= $this->formresult;
      $this->formvalues= $this->formresult->addChild(new xml::Node('formvalues'));
      $this->formerrors= $this->formresult->addChild(new xml::Node('formerrors'));
    }

  }
?>
