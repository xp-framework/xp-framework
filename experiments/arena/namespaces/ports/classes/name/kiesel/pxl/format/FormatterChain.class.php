<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  namespace name::kiesel::pxl::format;

  ::uses(
    'name.kiesel.pxl.format.AbstractFormatter',
    'name.kiesel.pxl.format.SimpleMarkupFormatter',
    'name.kiesel.pxl.format.URLFormatter',
    'name.kiesel.pxl.format.NewlineFormatter'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class FormatterChain extends AbstractFormatter {
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function __construct() {
      $this->formatters= array(
        new SimpleMarkupFormatter(),
        new URLFormatter(),
        new NewlineFormatter()
      );
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function apply($string) {
      foreach ($this->formatters as $f) {
        $string= $f->apply($string);
      }
      
      return $string;
    }
  }
?>
