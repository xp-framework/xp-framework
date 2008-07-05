<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.http.HttpTransport', 'io.streams.MemoryInputStream');

  /**
   * Transport via curl functions
   *
   * @ext      curl
   * @see      xp://peer.http.HttpConnection
   * @purpose  Transport
   */
  class CurlHttpTransport extends HttpTransport {
    protected
      $handle = NULL;

    /**
     * Constructor
     *
     * @param   peer.URL url
     */
    public function __construct(URL $url) {
      $this->handle= curl_init();
      curl_setopt($this->handle, CURLOPT_HEADER, 1);
      curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, 0);
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
      $curl= curl_copy_handle($this->handle);
      curl_setopt($curl, CURLOPT_URL, $request->url->getUrl());
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getRequestString());
      curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
      
      if ($this->proxy && !$this->proxy->isExcluded($request->getUrl())) {
        curl_setopt($curl, CURLOPT_PROXY, $this->proxy->host);
        curl_setopt($curl, CURLOPT_PROXYPORT, $this->proxy->port);
      }
      
      $response= curl_exec($curl);
      curl_close($curl);

      if (FALSE === $response) {
        throw new IOException(sprintf('%d: %s', curl_errno($curl), curl_error($curl)));
      }

      return new HttpResponse(new MemoryInputStream($response));
    }
  }
?>
