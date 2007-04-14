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
  class AntEnvironment extends Object {
    public
      $out  = NULL,
      $err  = NULL;
      
    protected
      $hashmap  = array();

    public function __construct($out, $err) {
      $this->out= $out;
      $this->err= $err;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function put($key, $value) {
      if (isset($this->hashmap[$key]))
        throw new IllegalArgumentException('Property ['.$key.'] already declared.');
      
      $this->hashmap[$key]= $value;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function get($key) {
      if (!isset($this->hashmap[$key]))
         throw new IllegalArgumentException('Property ['.$key.'] does not exist.');
        
      return $this->hashmap[$key];
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function substitute($string) {
      return preg_replace_callback('#\$\{([^\}]+)\}#', array($this, 'replaceCallback'), $string);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function replaceCallback($matches) {
      return $this->get($matches[1]);
    }
  }
?>
