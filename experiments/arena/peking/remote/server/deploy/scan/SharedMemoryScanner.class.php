<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'remote.server.deploy.Deployment',
    'io.sys.ShmSegment'
  );

  /**
   * Deployment scanner
   *
   * @purpose  Interface
   */
  class SharedMemoryScanner extends Object {

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->storage= &new ShmSegment(0x3c872747);
    }
  
    /**
     * Scan if deployments changed
     *
     * @access  public
     * @return  bool 
     */
    function scanDeployments() {
      if ($this->storage->isEmpty()) return FALSE;
      return TRUE;
    }

    /**
     * Get a list of deployments
     *
     * @access  public
     * @return  remote.server.deploy.Deployable[]
     */
    function &getDeployments() {
      return $this->storage->get();
    }
  } implements(__FILE__, 'remote.server.deploy.scan.DeploymentScanner');
?>
