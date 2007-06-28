<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command', 'HttpProtocol', 'handlers.FileHandler');

  /**
   * HTTP server runner
   *
   * @purpose  Server runner
   */
  class HttpService extends Command {
    protected 
      $model= NULL,
      $ip   = '',
      $port = 0;

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
     * Set bind IP - defaults to: 127.0.0.1 (localhost)
     *
     * @param   string ip default '127.0.0.1'
     */
    #[@arg]
    public function setIp($ip= '127.0.0.1') {
      $this->ip= $ip;
    }

    /**
     * Set bind port - defaults to 80 (HTTP). You may encounter problems
     * on Un*x systems if you are not logged on as root, use 8080 or 8081
     * instead in this case.
     *
     * @param   int port default 80
     */
    #[@arg]
    public function setPort($port= 80) {
      $this->port= $port;
    }
    
    /**
     * Runs this server. Terminate by pressing ^C in the shell you start
     * this server from.
     *
     */
    public function run() {
      $this->out->writeLine('---> Binding ', $this->ip, ':', $this->port);
      $server= $this->model->newInstance($this->ip, $this->port);
      with ($protocol= $server->setProtocol(new HttpProtocol())); {
        $protocol->setUrlHandler('/.*/', new FileHandler(realpath('../../people/friebe/xp-redesign/')));
      }
      $server->init();
      $this->out->writeLine('===> Server started');
      $server->service();
      $server->shutdown();
    }
  }
?>
