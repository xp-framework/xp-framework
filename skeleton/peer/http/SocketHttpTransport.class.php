<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.HttpTransport', 'peer.Socket', 'peer.SocketInputStream');

  /**
   * Transport via sockets
   *
   * @see      xp://peer.Socket
   * @see      xp://peer.http.HttpConnection
   * @purpose  Transport
   */
  class SocketHttpTransport extends HttpTransport {
    protected
      $socket      = NULL,
      $proxySocket = NULL;

    /**
     * Constructor
     *
     * @param   peer.URL url
     */
    public function __construct(URL $url) {
      $this->socket= $this->newSocket($url);
    }

    /**
     * Creates a socket
     *
     * @param   peer.URL url
     * @return  peer.Socket
     */
    protected function newSocket(URL $url) {
      return new Socket($url->getHost(), $url->getPort(80));
    }

    /**
     * Set proxy
     *
     * @param   peer.http.HttpProxy proxy
     */
    public function setProxy(HttpProxy $proxy) {
      parent::setProxy($proxy);
      $this->proxySocket= $this->newSocket(create(new URL())->setHost($proxy->host)->setPort($proxy->port));
    }
    
    /**
     * Sends a request
     *
     * @param   peer.http.HttpRequest request
     * @param   int timeout default 60
     * @param   float connecttimeout default 2.0
     * @return  peer.http.HttpResponse response object
     */
    public function send(HttpRequest $request, $timeout= 60, $connecttimeout= 2.0) {

      // Use proxy socket and Modify target if a proxy is to be used for this request, 
      // a proxy wants "GET http://example.com/ HTTP/X.X"
      if ($this->proxy && !$this->proxy->isExcluded($url= $request->getUrl())) {
        $request->setTarget(sprintf(
          '%s://%s%s%s',
          $url->getScheme(),
          $url->getHost(),
          $url->getPort() ? ':'.$url->getPort() : '',
          $url->getPath('/')
        ));

        $s= $this->proxySocket;
      } else {
        $s= $this->socket;
      }
    
      $s->setTimeout($timeout);
      $s->connect($connecttimeout);
      $s->write($request->getRequestString());

      return new HttpResponse(new SocketInputStream($s));
    }
  }
?>
