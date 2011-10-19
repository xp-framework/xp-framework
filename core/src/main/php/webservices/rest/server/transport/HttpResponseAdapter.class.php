<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * HTTP response adapter interface
   *
   */
  interface HttpResponseAdapter {
    
    /**
     * Set status code
     * 
     * @param int code The status code
     */
    public function setStatus($code);
    
    /**
     * Set header
     * 
     * @param string name The header name
     * @param string value The value
     * @return string
     */
    public function setHeader($name, $value);
    
    /**
     * Set body
     * 
     * @param var[] data The data
     */
    public function setData($data);
  }
?>
