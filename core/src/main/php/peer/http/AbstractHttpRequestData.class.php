<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'peer.http.RequestData',
    'lang.IllegalArgumentException'
  );

  /**
   * AbstractHttpRequestData
   *
   * General object to handle all basic functionality. mainly header stuff
   * of the HttpRequestData and requestDataMulti
   *
   * @see      xp://peer.http.HttpRequest#setBody
   * @see      xp://peer.http.HttpRequest#setParameters
   * @purpose  Pass request data directly to
   */
  abstract class AbstractHttpRequestData extends RequestData {

    protected
      $headers= array();

    /**
     * Constructor
     *
     * Params might be extended with language, location, MD5 and/or range
     * Objects may be given, but by default only objects implementing Serializable
     * will not trigger an exception. Additionally text/plain will be used for them by default.
     *
     * @param   int/string/array data
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($data) {
      parent::__construct($data);
      $this->setDefaultHeaders();
    }

    /**
     * Returns the default type.
     *
     * @return string
     */
    abstract protected function getDefaultType();

    /**
     * Will set the default headers, which will be used if not overwritten later
     *
     * @return void
     */
    protected function setDefaultHeaders() {
      $defaultHeaders= array(
        $this->getDefaultHeader_ContentLength(),
        $this->getDefaultHeader_ContentType()
      );
      $this->addHeaders($defaultHeaders);
    }

    /**
     * Will return the default content type header
     *
     * @return peer.Header
     */
    protected function getDefaultHeader_ContentType() {
      $defaultType= $this->getDefaultType();
      return new Header('Content-Type', $defaultType);
    }

    /**
     * Will return the default content length header
     *
     * @return peer.Header
     */
    protected function getDefaultHeader_ContentLength() {
      return new Header('Content-Length', 0);
    }

    /**
     * Will add the given header (array or peer.Header) to the list of headers
     * if an array is given, the peer.Header will get instatiated
     *
     * @param array|peer.Header header
     * @return void
     * @throws lang.IllegalArgumentException on illegal arguments
     */
    public function addHeader($header) {
      if(is_array($header)) {
        $header= array_values($header);
        $header= new Header($header[0], $header[1]);
      }
      if(!$header instanceof Header) {
        throw new IllegalArgumentException('Header must either be of type Header or array');
      }
      $name= $header->getName();
      $this->headers[$name]= $header;
    }

    /**
     * Will set multiple Headers from the given array data
     *
     * @param   array headers
     * @return  void
     * @see     peer.http.HttpRequestData#addHeader
     */
    public function addHeaders($headers= array()) {
      $headers= (array)$headers;
      foreach($headers as $header) {
        $this->addHeader($header);
      }
    }

    /**
     * Same as peer.http.HttpRequestData#addHeader, but returns self.
     *
     * @param   array|peer.Header header
     * @return  peer.http.AbstractHttpRequestData this
     * @throws  lang.IllegalArgumentException on illegal arguments
     */
    public function withHeader($header) {
      $this->addHeader($header);
      return $this;
    }

    /**
     * Same as peer.http.HttpRequestData#addHeaders, but returns self.
     *
     * @param   array headers
     * @return  peer.http.AbstractHttpRequestData this
     * @throws  lang.IllegalArgumentException on illegal arguments
     * @see     peer.http.HttpRequestData#addHeaders
     */
    public function withHeaders($headers= array()) {
      $headers= (array)$headers;
      foreach($headers as $header) {
        $this->addHeader($header);
      }
      return $this;
    }

    /**
     * Will return the header(s) for the given type if exists.
     * Returns NULL if not set
     *
     * @param   string type
     * @return  [:var] peer.Header, array of peer.Header or null if none found
     */
    public function getHeadersForType($name) {
      if($this->hasHeader($name)) {
        return $this->headers[$name];
      }
      return NULL;
    }

    /**
     * Will return if a header for the given type is set
     *
     * @param   string type
     * @return  bool
     */
    public function hasHeader($name) {
      return array_key_exists($name, $this->headers);
    }

    /**
     * Return list of HTTP headers to be set on
     * behalf of the data
     *
     * @return  peer.Header[]
     */
    public function getHeaders() {
      $headers= parent::getHeaders();
      foreach($this->headers as $header) {
        if('Content-Length' === $header->getName()) {
          $header->value= strlen($this->getData());
        }
        $headers[]= $header;
      }
      return $headers;
    }
  }
?>
