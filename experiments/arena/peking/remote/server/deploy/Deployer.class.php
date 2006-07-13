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
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function deployBean(&$class, &$containerManager) {
      $log= &Logger::getInstance();
      $cat= &$log->getCategory($this->getClassName());
      
      // Fetch naming directory
      $directory= &NamingDirectory::getInstance();
      
      $cat->info($this->getClassName(), 'Begin deployment of', $class->getName());
      
      try(); {

        // Load class' directory name
        if (!$class->hasAnnotation(DEPLOY_LOOKUP_KEY)) return $this->_deployException(
          'Cannot deploy class without @directoryName annotation.'
        );

        $directoryName= $class->getAnnotation(DEPLOY_LOOKUP_KEY);

        // Fetch class' peer classname
        if (
          !$class->hasAnnotation(DEPLOY_PEERCLASS_KEY) && 
          !$class->hasAnnotation(DEPLOY_HOMEINTERFACE_KEY)
        ) return $this->_deployException(
          'Peer class for "'.$class->getName().'" not found.'
        );
        
        // Create beanContainer
        $beanContainer= &BeanContainer::forClass($class);
        $containerManager->register($beanContainer);
        
        if ($class->hasAnnotation(DEPLOY_PEERCLASS_KEY)) {
          $peerCN= &$class->getAnnotation(DEPLOY_PEERCLASS_KEY);
          $peerClass= &XPClass::forName($peerCN);
        } else {
          $peerClass= &$this->generatePeerFor($class);
        }
        
        $homeInterface= NULL;

        foreach ($peerClass->getInterfaces() as $interface) {
          if ($interface->isSubclassOf('remote.beans.HomeInterface')) {
            $homeInterface= &$interface;
            break;
          }
        }
        
        if (!$homeInterface) return $this->_deployException(
          'HomeInterface for "'.$class->getName().'" not found.'
        );
        
        $beanContainer->setPeer($peerClass->newInstance());
        $beanContainer->setInvocationStrategy(new StatelessSessionInvocationStrategy());

        $invocationHandler= &new ContainerInvocationHandler();
        $invocationHandler->setContainer($beanContainer);
        $invocationHandler->setType(INVOCATION_TYPE_HOME);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      

      // Now bind into directory
      $directory->bind($directoryName, Proxy::newProxyInstance(
        ClassLoader::getDefault(),
        InterfaceUtil::getUniqueInterfacesFor($peerClass),
        $invocationHandler
      ));
     
      $cat->info($this->getClassName(), 'End deployment of', $class->getName(), 'with ND entry', $directoryName);
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
     
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &generatePeerFor(&$class) {
      static $peers= array();
      
      if (isset($peers[$class->getName()])) return $peers[$class->getName()];
      
      $peerFQCN= $class->getName().'Peer';
      $peerCN= substr($peerFQCN, strrpos($peerFQCN, '.')+ 1);
      
      $cl= &ClassLoader::getDefault();
      $home= &$cl->defineClass($peerCN, sprintf('class %s extends Object {
          /**
           * Create method
           *
           * @access  public
           * @return  &%s
           */
          function create() {
            $instance= &new %s();
            return $instance;
          }
        } implements("%s", "%s");
        ',
        $peerCN,
        $class->getName(),
        xp::reflect($class->getName()),
        strtr($peerFQCN, '.', '/').'.class.php',
        $class->getAnnotation(DEPLOY_HOMEINTERFACE_KEY)
      ));
      
      $peers[$class->getName()]= &$home;
      return $home;
    }
  }
?>
