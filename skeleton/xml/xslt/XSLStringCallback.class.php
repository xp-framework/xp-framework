<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * XSL callbacks for string operations
   *
   * @purpose   XSL callback
   * @test      xp://net.xp_framework.unittest.xml.XslCallbackTest
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
    
    /**
     * Substitute one string through another in a given string
     *
     * @param   string str
     * @param   string search
     * @param   string replace
     * @return  string
     */
    #[@xslmethod]
    public function replace($str, $search, $replace) {
      return str_replace($search, $replace, $str);
    }
    
    /**
     * Convert newlines to <br/>
     *
     * @param   string string
     * @return  string
     */
    #[@xslmethod]
    public function nl2br($string) {
      return nl2br($string);
    }
  }
?>
