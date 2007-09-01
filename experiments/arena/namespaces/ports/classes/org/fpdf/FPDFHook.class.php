<?php
/* This class is part of the XP framework
 *
 * $Id: FPDFHook.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::fpdf;

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
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    public function onEndPage($pdf, $page);
  
  }
?>
