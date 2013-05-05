<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.RestParamSource');

  /**
   * REST route interface
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.RestRouteTest
   */
  class RestRoute extends Object {
    protected $verb= '';
    protected $path= '';
    protected $handler= NULL;
    protected $target= NULL;
    protected $accepts= array();
    protected $produces= array();
    protected $params= array();
    
    /**
     * Constructor
     * 
     * @param  string verb
     * @param  string path
     * @param  lang.XPClass handler
     * @param  lang.reflect.Method target
     * @param  string[] accepts
     * @param  string[] produces
     */
    public function __construct($verb, $path, $handler, $target, $accepts, $produces) {
      $this->verb= strtoupper($verb);
      $this->path= $path;
      $this->handler= $handler;
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
     * Get path pattern
     *
     * @return string
     */
    public function getPattern() {
      return '#^'.preg_replace('/\{([\w]*)\}/', '(?P<$1>[^/]+)', $this->path).'$#';
    }

    /**
     * Get segments
     *
     * @param  string path
     * @return [:string] segments
     */
    public function appliesTo($path) {
      return preg_match($this->getPattern(), $path, $segments) ? $segments : NULL;
    }

    /**
     * Get handler
     *
     * @return lang.XPClass
     */
    public function getHandler() {
      return $this->handler;
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

    /**
     * Add a parameter
     *
     * @param  string name
     * @param  webservices.rest.srv.RestParamSource source
     */
    public function addParam($name, $source) {
      $this->params[$name]= $source;
    }

    /**
     * Gets all parameters
     *
     * @param  [:webservices.rest.srv.RestParamSource]
     */
    public function getParams() {
      return $this->params;
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      $params= '';
      foreach ($this->params as $name => $source) {
        $params.= ', @$'.$name.': '.$source->toString();
      }
      return sprintf(
        '%s(%s %s%s -> %s %s::%s(%s)%s)',
        $this->getClassName(),
        $this->verb,
        $this->path,
        NULL === $this->accepts ? '' : ' @ '.implode(', ', $this->accepts),
        $this->target->getReturnTypeName(),
        $this->handler->getName(),
        $this->target->getName(),
        substr($params, 2),
        NULL === $this->produces ? '' : ' @ '.implode(', ', $this->produces)
      );
    }
  }
?>
