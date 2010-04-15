<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.System',
    'util.log.Logger',
    'peer.URL',
    'org.nagios.nsca.NscaClient'
  );

  /**
   * Heartbeat sender.
   *
   * A heartbeat sender is a class that send a living sign (`heartbeat`)
   * to a given NSCA server. The NSCA server forwards it to the
   * Nagios system.
   *
   * Heartbeats can be used in crons or long living applications to simplify
   * surveillance of system health.
   *
   * Example:
   * <code>
   *   // Program init
   *   Heartbeat::getInstance()->setup('nagios://nagios.host.name.com:5667/service_name');
   *
   *   // Program execution
   *   ...
   *
   *   // Program exit
   *   Heartbeat::getInstance()->send(NSCA_OK, 'Service ran successfully.');
   * </code>
   *
   * @see      http://nagios.org/
   * @purpose  Nagios integration
   */
  class Heartbeat extends Object {
    public
      $server   = '',
      $port     = 5667,
      $version  = NSCA_VERSION_2,
      $service  = NULL,
      $host     = '';

    protected static
      $instance   = NULL;

    /**
     * Get instance of this class.
     *
     * @return  org.nagios.nsca.Heartbeat
     */
    public static function getInstance() {
      if (NULL === self::$instance) self::$instance= new Heartbeat();
      return self::$instance;
    }

    /**
     * Setup the class instance. A dsn string must be given with the relevant information
     * about the server and script:
     *
     * Eg: nagios://nagios.xp_framework.net:5667/service_to_monitor
     *
     * @param   string dsn
     */
    public function setup($dsn) {
      $url= new URL($dsn);

      $this->server=  $url->getHost();
      $this->port=    $url->getPort(5667);
      $this->version= $url->getParam('version', NSCA_VERSION_2);
      $this->service= trim($url->getPath(), '/');
      $this->host=    $url->getParam('hostname', System::getProperty('host.name'));
      if (FALSE !== $url->getParam('domain', FALSE)) {
        $this->host.= '.'.ltrim($url->getParam('domain'), '.');
      }
    }

    /**
     * Sends a heartbeat to nagios, if the server is not reachable, do not throw
     * an exception.
     *
     * In most of the cases, if the nsca server is not reachable temporarily, getting
     * exceptions due to that is not what is wished. For the cases where one wants
     * to be able to handle it in other means, the sendRaw() method has been introduced
     * which is used internally, too.
     *
     * @param   int status status of service (one of NSCA_OK, NSCA_WARN, NSCA_ERROR, NSCA_UNKNOWN)
     * @param   string message default ''
     */
    public function emit($status, $message= '') {
      try {
        $this->send($status, $message);
      } catch (IOException $ignore) {
      }
    }

    /**
     * Sends a heartbeat to nagios
     *
     * @param   int status status of service (one of NSCA_OK, NSCA_WARN, NSCA_ERROR, NSCA_UNKNOWN)
     * @param   string message default ''
     * @throws  io.IOException in case
     */
    public function send($status, $message= '') {
      $nsca= new NscaClient(
        $this->server,
        $this->port,
        $this->version,
        NSCA_CRYPT_XOR
      );

      $nsca->connect();
      $nsca->send(new NscaMessage($this->host, $this->service, $status, $message));
      $nsca->close();
    }
  }
?>
