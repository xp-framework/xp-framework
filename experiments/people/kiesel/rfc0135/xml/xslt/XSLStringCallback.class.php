<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * XSL callbacks for string operations
   *
   * @purpose   XSL callback
   */
  class XSLStringCallback extends Object {
  
    /**
     * urlencode() string
     *
     * @param   string string
     * @return  string
     */
    #[@xslmethod]
    public function urlencode($string) {
      return urlencode($string);
    }
    
    /**
     * urldecode() string
     *
     * @param   string string
     * @return  string
     */
    #[@xslmethod]
    public function urldecode($string) {
      return urldecode($string);
    }
    
    /**
     * strtolower() string
     *
     * @param   string string
     * @return  string
     */
    #[@xslmethod]
    public function strtolower($string) {
      return strtolower($string);
    }    

    /**
     * strtoupper() string
     *
     * @param   string string
     * @return  string
     */
    #[@xslmethod]
    public function strtoupper($string) {
      return strtoupper($string);
    }    
  }
?>
