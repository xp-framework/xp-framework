<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * REST route interface
   *
   */
  class RestRoute extends Object {
    protected $verb= '';
    protected $path= '';
    protected $target= NULL;
    protected $accepts= array();
    protected $produces= array();
    
    /**
     * Constructor
     * 
     * @param  string verb
     * @param  string path
     * @param  lang.reflect.Method target
     * @param  string[] accepts
     * @param  string[] produces
     */
    public function __construct($verb, $path, $target, $accepts, $produces) {
      $this->verb= $verb;
      $this->path= $path;
      $this->target= $target;
      $this->accepts= $accepts;
      $this->produces= $produces;
    }

    /**
     * Get verb
     *
     * @return string
     */
    public function getVerb() {
      return $this->verb;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
      return $this->path;
    }

    /**
     * Get target
     *
     * @return lang.reflect.Method
     */
    public function getTarget() {
      return $this->target;
    }

    /**
     * Get what is accepted
     *
     * @return string[]
     */
    public function getAccepts($default= NULL) {
      return NULL === $this->accepts ? $default : $this->accepts;
    }

    /**
     * Get what is produced
     *
     * @return string[]
     */
    public function getProduces($default= NULL) {
      return NULL === $this->produces ? $default : $this->produces;
    }
  }
?>
