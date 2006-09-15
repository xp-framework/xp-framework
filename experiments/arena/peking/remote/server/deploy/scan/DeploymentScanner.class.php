<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.server.deploy.Deployment');

  /**
   * Deployment scanner
   *
   * @purpose  Interface
   */
  class DeploymentScanner extends Interface {
  
    /**
     * Scan if deployments changed
     *
     * @access  public
     * @return  bool 
     */
    function scanDeployments() { }

    /**
     * Get a list of deployments
     *
     * @access  public
     * @return  remote.server.deploy.Deployable[]
     */
    function getDeployments() { }

  }
?>
