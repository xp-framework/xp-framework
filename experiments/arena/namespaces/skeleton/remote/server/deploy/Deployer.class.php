<?php
/* This class is part of the XP framework
 *
 * $Id: Deployer.class.php 9370 2007-01-25 14:16:11Z gelli $ 
 */

  namespace remote::server::deploy;

  uses(
    'util.log.Logger',
    'remote.server.deploy.DeployException',
    'remote.server.container.StatelessSessionBeanContainer',
    'remote.server.naming.NamingDirectory',
    'remote.server.ContainerInvocationHandler',
    'util.log.Traceable'
  );

  define('DEPLOY_LOOKUP_KEY',         'lookupName');
  define('DEPLOY_PEERCLASS_KEY',      'peerClass');
  define('DEPLOY_HOMEINTERFACE_KEY',  'homeInterface');

  /**
   * Deployer
   *
   * @purpose  Deployer
   */
  class Deployer extends lang::Object implements util::log::Traceable {
    protected
      $cat      = NULL;
  
    /**
     * Deploy
     *
     * @param   remote.server.deploy.Deployable deployment
     */
    public function deployBean($deployment) {
      if (is('IncompleteDeployment', $deployment)) {
        throw(new DeployException(
          'Incomplete deployment originating from '.$deployment->origin, 
          $deployment->cause
        ));
      }

      $this->cat && $this->cat->info($this->getClassName(), 'Begin deployment of', $deployment);

      // Put bean's xar file into include_path - uses() within the beans will be able to resolve
      // references to other classes inside the xar.
      // This is a necessary HACK atm.
      $cl= $deployment->getClassLoader();
      $org= ini_get('include_path');
      ini_set('include_path', $org.PATH_SEPARATOR.$cl->archive->file->getURI());

      $impl= $cl->loadClass($deployment->getImplementation());
      $interface= $cl->loadClass($deployment->getInterface());

      $directoryName= $deployment->getDirectoryName();

      // Fetch naming directory
      $directory= remote::server::naming::NamingDirectory::getInstance();

      // Create beanContainer
      // TBI: Check which kind of bean container has to be created
      $beanContainer= remote::server::container::StatelessSessionBeanContainer::forClass($impl);
      $this->cat && $beanContainer->setTrace($this->cat);

      // Create invocation handler
      $invocationHandler= new remote::server::ContainerInvocationHandler();
      $invocationHandler->setContainer($beanContainer);

      // Now bind into directory
      $directory->bind($directoryName, lang::reflect::Proxy::newProxyInstance(
        $cl,
        array($interface),
        $invocationHandler
      ));
      
      $this->cat && $this->cat->info($this->getClassName(), 'End deployment of', $impl->getName(), 'with ND entry', $directoryName);

      // Leave xar in include_path - classes might load some dependencies at runtime
      return $beanContainer;
    }
    
    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) { 
      $this->cat= $cat;
    }
  } 
?>
