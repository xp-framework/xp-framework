<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('org.fpdf.FPDFHook');

  /**
   * FPDFHook interface implementation that does nothing
   *
   * @see      xp://org.fpdf.FPDFHook
   * @purpose  Base class
   */
  class FPDFHookImpl extends Object implements FPDFHook {
  
    /**
     * Gets called when a page is finalized
     *
     * @access  public
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    public function onEndPage(&$pdf, $page) { 
    }
  
  } 
?>
