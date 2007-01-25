<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.server.deploy.Deployment',
    'io.sys.ShmSegment',
    'remote.server.deploy.scan.DeploymentScanner'
  );

  /**
   * Deployment scanner
   *
   * @purpose  Interface
   */
  class SharedMemoryScanner extends Object implements DeploymentScanner {

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->storage= new ShmSegment(0x3c872747);
    }
  
    /**
     * Scan if deployments changed
     *
     * @return  bool 
     */
    public function scanDeployments() {
      if ($this->storage->isEmpty()) return FALSE;
      return TRUE;
    }

    /**
     * Get a list of deployments
     *
     * @return  remote.server.deploy.Deployable[]
     */
    public function getDeployments() {
      return $this->storage->get();
    }
  } 
?>
