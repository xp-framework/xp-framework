<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('name.kiesel.pxl.format.AbstractFormatter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class URLFormatter extends AbstractFormatter {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function apply($string) {
      return preg_replace_callback('#(^|\s+)(\S+://\S+)(\s+|$)#', array($this, 'buildLink'), $string);
    }  
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function buildLink($matches) {
      return sprintf('<a href="%1$s">%1$s</a>', $matches[2]);
    }
  }
?>
