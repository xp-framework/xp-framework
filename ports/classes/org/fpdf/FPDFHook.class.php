<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Hook interface
   *
   * @see      xp://org.fpdf.FPDF#addHook
   * @purpose  Interface
   */
  class FPDFHook extends Interface {
  
    /**
     * Gets called when a page is finalized
     *
     * @access  public
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    function onEndPage(&$pdf, $page) { }
  
  }
?>
