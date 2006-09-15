<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'lang.Thread',
    'io.sys.ShmSegment'
  );

  /**
   * Deployment scanner for the easc server
   *
   * @purpose  Thread
   */
  class ScannerThread extends Thread {
    var
      $scanner    = NULL,
      $period     = 60;


    /**
     * Constructor
     *
     * @access  public
     * @param   &remote.server.deploy.scan.DeploymentScanner scanner
     */
    function __construct(&$scanner) {
      parent::__construct('scanner');
      $this->scanner= &$scanner;
      
      $this->storage= &new ShmSegment(0x3c872747);
    }

    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) { 
      $this->cat= &$cat;
    }

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function distribute($deployments) {
      if (!$this->storage->isEmpty()) $this->storage->remove();
      $this->storage->put($deployments);
    }


    /**
     * Set scan period
     *
     * @access  public
     * @param   int period
     * @throws  lang.IllegalArgumentException
     */
    function setScanPeriod($period) {
      if ($period <= 0) {
        return throw(new IllegalArgumentException('ScanPeriod must be > 0; have: ', $period));
      }

      $this->period= $period;
    }


    /**
     * Periodically scan for deployments
     *
     * @access  public
     */
    function run() {
      $initial= TRUE;
      do {
        $changed= $this->scanner->scanDeployments();
        if ($changed) {
          $this->distribute($deployments= $this->scanner->getDeployments());
          $this->cat && $this->cat->debugf('Scanner #%s: Deployments= %s', $this->_id, xp::stringOf($deployments));
          
          // Check for *RE*-deployment
          if (!$initial) {
            $this->cat && $this->cat->infof('Scanner #%s: Deployments changed, signalling server to redeploy', $this->_id);
            posix_kill($this->_pid, SIGHUP);
          }
          $initial= FALSE;
        }
        
        Thread::sleep($this->period * 1000);
      } while (1);
    }

  } implements(__FILE__, 'util.log.Traceable');
?>
