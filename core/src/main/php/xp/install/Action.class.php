<?php
  namespace xp\install;

  /**
   * Abstract base class for all actions
   */
  abstract class Action extends \lang\Object implements \util\log\Traceable {
    protected $api;
    protected $cat= NULL;

    /**
     * Creates a new action
     *
     * @param  webservices.api.RestClient $api
     */
    public function __construct($api) {
      $this->api= $api;
    }

    /**
     * Set trace
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
      $this->api->setTrace($cat);
    }
  }
?>