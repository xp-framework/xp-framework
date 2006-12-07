<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.log.Logger',
    'remote.server.deploy.DeployException',
    'remote.server.BeanContainer',
    'remote.server.naming.NamingDirectory',
    'remote.server.ContainerInvocationHandler',
    'remote.reflect.InterfaceUtil',
    'remote.server.strategy.StatelessSessionInvocationStrategy'
  );

  define('DEPLOY_LOOKUP_KEY',         'lookupName');
  define('DEPLOY_PEERCLASS_KEY',      'peerClass');
  define('DEPLOY_HOMEINTERFACE_KEY',  'homeInterface');

  /**
   * Deployer
   *
   * @purpose  Deployer
   */
  class Deployer extends Object {
  
    /**
     * Deploy
     *
     * @access  public
     * @param   remote.server.deploy.Deployable deployment
     * @param   remote.server.ContainerManager containerManager
     */
    function deployBean(&$deployment, &$containerManager) {

      if (is('IncompleteDeployment', $deployment)) {
        return throw(new DeployException(
          'Incomplete deployment originating from '.$deployment->origin, 
          $deployment->cause
        ));
      }

      $this->cat && $this->cat->info($this->getClassName(), 'Begin deployment of', $deployment);
      try(); {
        $cl= &$deployment->getClassLoader();
        
        $org= ini_get('include_path');
        ini_set('include_path', $org.PATH_SEPARATOR.$cl->archive->file->getURI());
        
        $impl= &$cl->loadClass($deployment->getImplementation());
        $interface= &$cl->loadClass($deployment->getInterface());
        
        ini_set('include_path', $org);
        
        $directoryName= $deployment->getDirectoryName();
        
        // Fetch naming directory
        $directory= &NamingDirectory::getInstance();
        
        // Create beanContainer
        $beanContainer= &BeanContainer::forClass($impl);
        $beanContainer->setInvocationStrategy(new StatelessSessionInvocationStrategy());
        $containerManager->register($beanContainer);
        
        // Create invocation handler
        $invocationHandler= &new ContainerInvocationHandler();
        $invocationHandler->setContainer($beanContainer);
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      // Now bind into directory
      $directory->bind($directoryName, Proxy::newProxyInstance(
        $cl,
        array($interface),
        $invocationHandler
      ));
     
      $this->cat && $this->cat->info($this->getClassName(), 'End deployment of', $impl->getName(), 'with ND entry', $directoryName);
      return $beanContainer;
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
     * Throw DeployException
     *
     * @access  protected
     * @param   string msg
     * @return  bool
     * @throws  remote.server.deploy.DeployException
     */
    function _deployException($msg) {
      $log= &Logger::getInstance();
      $this->cat && $this->cat= &$log->getCategory($this->getClassName());
      $this->cat && $this->cat->warn($this->getClassName(), $msg);
      return throw(new DeployException($msg));
    }
  } implements (__FILE__, 'util.log.Traceable');
?>
