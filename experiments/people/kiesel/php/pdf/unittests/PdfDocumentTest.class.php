<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'PDFDocument'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class PdfDocumentTest extends TestCase {
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@test]
    function testCreation() {
      $doc= &new PDFDocument();
      $root= &$doc->getRootPage();
      $root->addChild(new PDFPage());
    }  
  }
?>
