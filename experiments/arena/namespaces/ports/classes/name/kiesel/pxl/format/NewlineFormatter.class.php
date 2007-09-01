<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::format;

  ::uses('name.kiesel.pxl.format.AbstractFormatter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class NewlineFormatter extends AbstractFormatter {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function apply($string) {
      return preg_replace("#\r?\n#", '<br/>', $string);
    }
  }
?>
