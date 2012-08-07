<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'lang.Thread',
    'io.sys.ShmSegment',
    'util.log.Traceable'
  );

  /**
   * Deployment scanner for the easc server
   *
   * @purpose  Thread
   */
  class ScannerThread extends Thread implements Traceable {
    public
      $scanner    = NULL,
      $period     = 60;
      
    protected
      $terminate  = FALSE;

    /**
     * Constructor
     *
     * @param   remote.server.deploy.scan.DeploymentScanner scanner
     */
    public function __construct($scanner) {
      parent::__construct('scanner');
      $this->scanner= $scanner;
      
      $this->storage= new ShmSegment(0x3c872747);
      if (!$this->storage->isEmpty()) $this->storage->remove();
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Distribute deployments
     *
     * @param   remote.server.deploy.Deployable[] deployments
     */
    public function distribute($deployments) {
      if (!$this->storage->isEmpty()) $this->storage->remove();
      $this->storage->put($deployments);
    }


    /**
     * Set scan period
     *
     * @param   int period
     * @throws  lang.IllegalArgumentException
     */
    public function setScanPeriod($period) {
      if ($period <= 0) {
        throw new IllegalArgumentException('ScanPeriod must be > 0; have: ', $period);
      }

      $this->period= $period;
    }

    /**
     * Periodically scan for deployments
     *
     */
    public function run() {
      $initial= TRUE;
      while (!$this->terminate) {
        $changed= $this->scanner->scanDeployments();
        if ($changed) {
          $this->distribute($deployments= $this->scanner->getDeployments());
          $this->cat && $this->cat->debugf('Scanner #%s: Deployments= %s', $this->_id, xp::stringOf($deployments));
          
          // Check for *RE*-deployment
          if (!$initial) {
            $this->cat && $this->cat->debugf('Scanner #%s: Deployments changed, signalling server to redeploy', $this->_id);
            posix_kill($this->_pid, SIGHUP);
          }
          $initial= FALSE;
        }
        
        Thread::sleep($this->period * 1000);
      }
    }
  } 
?>
