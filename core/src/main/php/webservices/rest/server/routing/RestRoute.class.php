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
    protected $path= '';
    protected $target= NULL;
    protected $accepts= array();
    protected $returns= array();
    
    /**
     * Constructor
     * 
     * @param  string path
     * @param  lang.reflect.Method target
     * @param  string[] accepts
     * @param  string returns
     */
    public function __construct($path, $target, $accepts, $returns) {
      $this->path= $path;
      $this->target= $target;
      $this->accepts= $accepts;
      $this->returns= $returns;
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
    public function getAccepts() {
      return $this->accepts;
    }

    /**
     * Get what is returned
     *
     * @return string
     */
    public function getReturns() {
      return $this->returns;
    }
  }
?>
