<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  interface ServerAPI {
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getHeaders();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getCookies();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getRequestParameters();        
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvValues();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvValue($key);
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setEnvValue($key, $value);

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setHeader($key, $value= NULL, $replace= FALSE);
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function headersSent();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function send($bytes);
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function flush();

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getEnvironment();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getStdinStream();
  }
?>
