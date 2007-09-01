<?php
/* This class is part of the XP framework
 *
 * $Id: DeploymentScanner.class.php 9151 2007-01-07 13:52:49Z kiesel $ 
 */

  namespace remote::server::deploy::scan;

  uses('remote.server.deploy.Deployment');

  /**
   * Deployment scanner
   *
   * @purpose  Interface
   */
  interface DeploymentScanner {
  
    /**
     * Scan if deployments changed
     *
     * @return  bool 
     */
    public function scanDeployments();

    /**
     * Get a list of deployments
     *
     * @return  remote.server.deploy.Deployable[]
     */
    public function getDeployments();

  }
?>
