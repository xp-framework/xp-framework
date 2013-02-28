<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Interface for log contexts to implement if they need
   * environmental information to be injected by the runners
   *
   */
  interface EnvironmentAware {

    /**
     * Setter for hostname
     *
     * @param  string hostname
     * @return void
     */
    public function setHostname($hostname);

    /**
     * Setter for runner
     *
     * @param  string runner
     * @return void
     */
    public function setRunner($runner);

    /**
     * Setter for instance
     *
     * @param  string instance
     * @return void
     */
    public function setInstance($instance);

    /**
     * Setter for resource
     *
     * @param  string resource
     * @return void
     */
    public function setResource($resource);

    /**
     * Setter for params
     *
     * @param  string params
     * @return void
     */
    public function setParams($params);
  }
?>
