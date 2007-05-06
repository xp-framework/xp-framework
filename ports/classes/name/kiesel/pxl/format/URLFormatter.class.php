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
      return sprintf('%s<a href="%2$s">%2$s</a>', $matches[1], $matches[2]);
    }
  }
?>
