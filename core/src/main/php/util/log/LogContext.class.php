<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Base class for all log context implementations
   *
   */
  abstract class LogContext extends Object {
    protected $hostname = NULL;
    protected $runner   = NULL;
    protected $instance = NULL;
    protected $resource = NULL;
    protected $params   = NULL;

    /**
     * Creates the string representation of this log context (to be written
     * to log files)
     *
     * @return string
     */
    public abstract function format();

    /**
     * Setter for hostname
     *
     * @param  string hostname
     * @return void
     */
    public function setHostname($hostname) {
      $this->hostname= $hostname;
    }

    /**
     * Getter for hostname
     *
     * @return string
     */
    public function getHostname() {
      return $this->hostname;
    }

    /**
     * Setter for runner
     *
     * @param  string runner
     * @return void
     */
    public function setRunner($runner) {
      $this->runner= $runner;
    }

    /**
     * Getter for runner
     *
     * @return string
     */
    public function getRunner() {
      return $this->runner;
    }

    /**
     * Setter for instance
     *
     * @param  string instance
     * @return void
     */
    public function setInstance($instance) {
      $this->instance= $instance;
    }

    /**
     * Getter for instance
     *
     * @return string
     */
    public function getInstance() {
      return $this->instance;
    }

    /**
     * Setter for resource
     *
     * @param  string resource
     * @return void
     */
    public function setResource($resource) {
      $this->resource= $resource;
    }

    /**
     * Getter for resource
     *
     * @return string
     */
    public function getResource() {
      return $this->resource;
    }

    /**
     * Setter for params
     *
     * @param  string params
     * @return void
     */
    public function setParams($params) {
      $this->params= $params;
    }

    /**
     * Getter for params
     *
     * @return string
     */
    public function getParams() {
      return $this->params;
    }

    /**
     * Creates a string representation of this object
     *
     * @return string
     */
    public function toString() {
      $s= $this->getClassName()."{\n";
      $s.= '  hostname='.$this->hostname."\n";
      $s.= '  runner='.$this->runner."\n";
      $s.= '  instance='.$this->instance."\n";
      $s.= '  resource='.$this->resource."\n";
      $s.= '  params='.$this->params."\n";
      return $s.'}';
    }
  }
?>
