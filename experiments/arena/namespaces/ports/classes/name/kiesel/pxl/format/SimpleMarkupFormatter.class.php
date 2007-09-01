<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::format;

  ::uses('name.kiesel.format.AbstractFormatter');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class SimpleMarkupFormatter extends name::kiesel::format::AbstractFormatter {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function apply($string) {
      return preg_replace_callback('#(^|\s+)([/*_])(\S.*\S)([/*_])($|[ .!?])#mU', array($this, 'format'), $string);
    }  
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function format($matches) {
      $markups= array(
        '*' => array('<b>', '</b>'),
        '/' => array('<i>', '</i>'),
        '_' => array('<u>', '</u>')
      );
      
      if (is_string($matches)) return $matches;
      if ($matches[2] != $matches[4]) return implode('', array_slice($matches, 1));
      
      return sprintf('%s%s%s%s%s',
        $matches[1],
        $markups[$matches[2]][0],
        $matches[3],
        $markups[$matches[2]][1],
        $matches[5]
      );
    }    
  }
?>
