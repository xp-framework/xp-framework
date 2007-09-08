<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.File', 
    'scriptlet.sapi.ServerAPI',
    'scriptlet.HttpScriptletEnvironment'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class WebserverAPI extends Object implements ServerAPI {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getHeaders() {
      // FIXME for CGI
      return getallheaders();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getCookies() {
      return $_COOKIE;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getRequestParameters() {
      return $_REQUEST;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvValues() {
      return $_ENV;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvValue($key) {
      return getenv($key);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setEnvValue($key, $value) {
      putenv($key.'='.$value);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getUploadFiles() {
      return $_FILES;
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setHeader($key, $value= NULL, $replace= FALSE) {
      if (!$value) {
        header($key);
        return;
      }
      
      header($key.': '.$value, $replace);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function headersSent() {
      return headers_sent();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function headersSentAt() {
      $res= headers_sent($file, $line);
      if (!$res) return FALSE;
      
      return array($file, $line);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function send($bytes) {
      echo $bytes;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function flush() {
      flush();
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvironment() {
      return HttpScriptletEnvironment::instanciate($this);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getStdinStream() {
      $f= new File('php://stdin');
      $f->open(FILE_MODE_READ);
      return $f;
    }
  }
?>
