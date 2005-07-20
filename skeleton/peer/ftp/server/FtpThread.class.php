<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Thread',
    'lang.reflect.Proxy'
  );
  
  define('LISTENER_CLASS',  'peer.ftp.server.FtpConnectionListener');

  /**
   * Server thread wich does all of the accept()ing on the sockets.
   *
   * @purpose   Thread
   * @model     Singleton
   */
  class FtpThread extends Thread {
    var
      $server                 = NULL,
      $terminate              = FALSE,
      $cat                    = NULL,
      $authenticatorHandler   = NULL,
      $storageHandler         = NULL;

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct('server');
    }

    /**
     * Set server
     *
     * @access  public
     * @param   &peer.server.Server server
     */
    function setServer(&$server) {
      $this->server= &$server;
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
     * Set an AuthenticationHandler
     *
     * @access  public
     * @param   &lang.reflect.InvokationHandler handler
     */
    function setAuthenticatorHandler(&$handler) {
      $this->authenticatorHandler= &$handler;
    }
    

    /**
     * Set a StorageHandler
     *
     * @access  public
     * @param   &lang.reflect.InvokationHandler handler
     */
    function setStorageHandler(&$handler) {
      $this->storageHandler= &$handler;
    }

    /**
     * Retrieve an instance of this thread
     *
     * @model   static
     * @access  protected
     * @return  &peer.ftp.server.FtpThread
     */
    function &getInstance() {
      static $instance= NULL;

      if (!$instance) $instance= new FtpThread();
      return $instance;
    }
    
    /**
     * Runs the server. Loads the listener using XPClass::forName()
     * so that the class is loaded within the thread's process space
     * and will be recompiled whenever the thread is restarted.
     *
     * @access  public
     * @throws  lang.Exception in case initializing the server fails
     * @throws  lang.SystemException in case setuid fails
     */
    function run() {
      try(); {
        with ($class= &XPClass::forName(LISTENER_CLASS), $cl= &ClassLoader::getDefault()); {
        
          // Add listener
          $listener= &$this->server->addListener($class->newInstance(
            Proxy::newProxyInstance(
              $cl,
              array(XPClass::forName('peer.ftp.server.storage.Storage')),
              $this->storageHandler
            ),
            Proxy::newProxyInstance(
              $cl,
              array(XPClass::forName('security.auth.Authenticator')),
              $this->authenticatorHandler
            )
          ));
        }

        // Enable debugging      
        if ($this->cat) {
          $listener->setTrace($this->cat);
          $this->server->setTrace($this->cat);
        }

        // Try to start the server
        $this->server->init();
      } if (catch('Exception', $e)) {
        $this->server->shutdown();
        return throw($e);
      }

      $this->server->service();
    }
  } implements (__FILE__, 'util.log.Traceable');
?>
