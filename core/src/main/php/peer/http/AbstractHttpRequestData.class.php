<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'peer.http.RequestData',
    'lang.IllegalArgumentException',
    'peer.HeaderFactory',
    'peer.header.AbstractContentHeader'
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
      return HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_TYPE, $defaultType);
    }

    /**
     * Will return the default content length header
     *
     * @return peer.Header
     */
    protected function getDefaultHeader_ContentLength() {
      return HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_LENGTH, 0);
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
        $header= call_user_func_array(array('HeaderFactory', 'getRequestHeader'), $header);
      }
      if(!$header instanceof Header) {
        throw new IllegalArgumentException('Header must either be of type Header or array');
      }
      $name= $header->getName();
      if(($header->isUnique()) ||
         (!array_key_exists($name, $this->headers))
      ) {
        // unique or first with this name
        $this->headers[$name]= $header;
      } else {
        $this->headers[$name]= (array)$this->headers[$name];
        $this->headers[$name][]= $header;
      }
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
    public function getHeadersForType($type) {
      if($this->hasHeader($type)) {
        $name= HeaderFactory::getNameForType($type);
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
    public function hasHeader($type) {
      try {
        $name= HeaderFactory::getNameForType($type);
      } catch (IllegalArgumentException $ex) {
        return FALSE;
      }
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
      $this->addHeaderToList($headers, $this->headers);
      return $headers;
    }

    /**
     * Will process all headers and flatten possible subheaders them
     *
     * @param   [:peer.http.Header] headers
     * @param   [:var] header peer.http.Header or [:peer.http.Header]
     * @return  void
     */
    protected function addHeaderToList(&$headers, $header) {
      if(is_array($header)) {
        foreach($header as $subHeader) {
          $this->addHeaderToList($headers, $subHeader);
        }
        return;
      }
      // HACK think about a better solution of how to identify Headers that use the content
      if($header instanceof AbstractContentHeader) {
        // set the latest content
        $header->setContent($this->getData());
      }
      $headers[]= $header;
    }
  }
?>
