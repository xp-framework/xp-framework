<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.http.HttpRequest', 
    'peer.http.HttpResponse',
    'peer.http.SocketHttpTransport',
    'peer.URL'
  );

  /**
   * Transport via sockets
   *
   * @see      xp://peer.http.HttpConnection
   * @purpose  Transport
   */
  abstract class HttpTransport extends Object {
    protected static
      $transports = array();
    
    protected
      $proxy      = NULL;
    
    static function __static() {
      self::$transports['http']= XPClass::forName('peer.http.SocketHttpTransport');
      
      // Depending on what extension is available, choose a different implementation 
      // for SSL transport. CURL is the slower one, so favor SSLSockets.
      if (extension_loaded('openssl')) {
        self::$transports['https']= XPClass::forName('peer.http.SSLSocketHttpTransport');
      } else if (extension_loaded('curl')) {
        self::$transports['https']= XPClass::forName('peer.http.CurlHttpTransport');
      }
    }
    
    /**
     * Constructor
     *
     * @param   peer.URL url
     */
    abstract public function __construct(URL $url);

    /**
     * Set proxy
     *
     * @param   peer.http.HttpProxy proxy
     */
    public function setProxy(HttpProxy $proxy) {
      $this->proxy= $proxy;
    }

    /**
     * Sends a request via this proxy
     *
     * @param   peer.http.HttpRequest request
     * @param   int timeout default 60
     * @param   float connecttimeout default 2.0
     * @return  peer.http.HttpResponse response object
     */
    abstract public function send(HttpRequest $request, $timeout= 60, $connecttimeout= 2.0);
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName();
    }
    
    /**
     * Register transport implementation for a specific scheme
     *
     * @param   string scheme
     * @param   lang.XPClass<peer.http.HttpTransport> class
     */
    public static function register($scheme, XPClass $class) {
      if (!$class->isSubclassOf('peer.http.HttpTransport')) {
        throw new IllegalArgumentException(sprintf(
          'Given argument must be lang.XPClass<peer.http.HttpTransport>, %s given',
          $class->toString()
        ));
      }
      self::$transports[$scheme]= $class;
    }
    
    /**
     * Get transport implementation for a specific URL
     *
     * @param   peer.URL url
     * @return  peer.http.HttpTransport
     * @throws  lang.IllegalArgumentException in case the scheme is not supported
     */
    public static function transportFor(URL $url) {
      $scheme= $url->getScheme();
      if (!isset(self::$transports[$scheme])) {
        throw new IllegalArgumentException('Scheme "'.$scheme.'" unsupported');
      }
      return self::$transports[$scheme]->newInstance($url);
    }
  }
?>
