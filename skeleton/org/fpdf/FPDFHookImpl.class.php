<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * FPDFHook interface implementation that does nothing
   *
   * @see      xp://org.fpdf.FPDFHook
   * @purpose  Base class
   */
  class FPDFHookImpl extends Object {
  
    /**
     * Gets called when a page is finalized
     *
     * @access  public
     * @param   &org.fpdf.FPDF pdf
     * @param   int page the number of the page
     */
    function onEndPage(&$pdf, $page) { 
    }
  
  } implements(__FILE__, 'org.fpdf.FPDFHook');
?>
