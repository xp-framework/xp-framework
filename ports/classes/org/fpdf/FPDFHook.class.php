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
  interface FPDFHook {
  
    /**
     * Gets called when a page is finalized
     *
     * @access  public
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    public function onEndPage(&$pdf, $page);
  
  }
?>
