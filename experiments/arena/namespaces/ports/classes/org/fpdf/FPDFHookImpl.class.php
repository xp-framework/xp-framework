<?php
/* This class is part of the XP framework
 *
 * $Id: FPDFHookImpl.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::fpdf;

  ::uses('org.fpdf.FPDFHook');

  /**
   * FPDFHook interface implementation that does nothing
   *
   * @see      xp://org.fpdf.FPDFHook
   * @purpose  Base class
   */
  class FPDFHookImpl extends lang::Object implements FPDFHook {
  
    /**
     * Gets called when a page is finalized
     *
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    public function onEndPage($pdf, $page) { 
    }
  
  } 
?>
