<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command', 'SessionProtocol', 'persist.FileSystemPersistence');

  /**
   * HTTP server runner
   *
   * @purpose  Server runner
   */
  class SessionService extends Command {
    protected 
      $model= NULL;

    protected static 
      $models= array(
        'prefork' => 'PreforkingServer',
        'fork'    => 'ForkingServer',
        'default' => 'Server',
      );
  
    /**
     * Set server model (one of prefork, fork or default)
     *
     * @param   string model default 'default'
     * @throws  lang.IllegalArgumentException in case the server model is unknown
     */
    #[@arg]
    public function setModel($model= 'default') {
      if (!isset(self::$models[$model])) {
        throw new IllegalArgumentException(sprintf(
          'Unknown server model "%s" (supported: [%s])',
          $model,
          implode(', ', array_keys(self::$models))
        ));
      }

      $this->model= Package::forName('peer.server')->loadClass(self::$models[$model]);
      $this->out->writeLine('---> Using server model ', $this->model->getName());
    }
    
    /**
     * Runs this server. Terminate by pressing ^C in the shell you start
     * this server from.
     *
     */
    public function run() {
      $server= $this->model->newInstance('172.17.29.15', 2001);
      $server->setTcpnodelay(TRUE);
      with ($protocol= $server->setProtocol(new SessionProtocol())); {
        $protocol->setPersistence(new FileSystemPersistence(dirname(__FILE__).'/data/'));
      }
      $server->init();
      $this->out->writeLine('===> Server started');
      $server->service();
      $server->shutdown();
    }
  }
?>
