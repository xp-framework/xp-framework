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

  // Constants for annotations
  define('DEPLOY_LOOKUP_KEY',         'lookupName');
  define('DEPLOY_PEERCLASS_KEY',      'peerClass');
  define('DEPLOY_HOMEINTERFACE_KEY',  'homeInterface');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Deployer extends Object {
  
    /**
     * Deploy
     *
     * @access  public
     * @param   remote.server.deploy.Deployable deployment
     * @param   remote.server.ContainerManager
     * @param   remote.server.BeanContainer
     */
    function deployBean(&$deployment, &$containerManager) {
      $log= &Logger::getInstance();
      $cat= &$log->getCategory($this->getClassName());

      if (is('IncompleteDeployment', $deployment)) {
        return throw(new DeployException(
          'Incomplete deployment originating from '.$deployment->origin, 
          $deployment->cause
        ));
      }

      $cat->info($this->getClassName(), 'Begin deployment of', $deployment);
      try(); {
        $cl= &$deployment->getClassLoader();
        $impl= &$cl->loadClass($deployment->getImplementation());
        $interface= &$cl->loadClass($deployment->getInterface());
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
     
      $cat->info($this->getClassName(), 'End deployment of', $impl->getName(), 'with ND entry', $directoryName);
      return $beanContainer;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _deployException($msg) {
      $log= &Logger::getInstance();
      $cat= &$log->getCategory($this->getClassName());
      $cat->warn($this->getClassName(), $msg);
      return throw(new DeployException($msg));
    }
  }
?>
