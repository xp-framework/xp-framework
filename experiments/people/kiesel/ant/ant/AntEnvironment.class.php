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
      $hashmap          = array(),
      $defaultExcludes  = array();

    public function __construct($out, $err) {
      $this->out= $out;
      $this->err= $err;
      $this->defaultExcludes= array(
        '**/*~',
        '**/#*#',
        '**/.#*',
        '**/%*%',
        '**/._*',
        '**/CVS',
        '**/CVS/**',
        '**/.cvsignore',
        '**/SCCS',
        '**/SCCS/**',
        '**/vssver.scc',
        '**/.svn',
        '**/.svn/**',
        '**/.DS_Store'
      );
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
    public function exists($key) {
      return isset($this->hashmap[$key]);
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
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function localUri($uri) {
      if ('/' == DIRECTORY_SEPARATOR) return $uri;
      return strtr($uri, '/', DIRECTORY_SEPARATOR);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getDefaultExcludes() {
      $patterns= array();
      foreach ($this->defaultExcludes as $e) {
        $patterns[]= new AntPattern($e);
      }
      return $patterns;
    }    
  }
?>
