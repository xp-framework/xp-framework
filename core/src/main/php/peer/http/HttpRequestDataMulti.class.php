<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'lang.IllegalArgumentException',
    'lang.IllegalStateException',
    'peer.Header',
    'peer.HeaderFactory',
    'peer.http.AbstractHttpRequestData',
    'peer.http.HttpConstants',
    'peer.http.HttpRequestData'
  );

  /**
   * HttpRequestDataMulti
   *
   * @see      xp://peer.http.HttpRequest#setBody
   * @see      xp://peer.http.HttpRequest#setBody
   * @purpose  Pass request data directly to
   */
  class HttpRequestDataMulti extends AbstractHttpRequestData {
    const
      DEFAULT_TYPE_MULTIPART= 'multipart/mixed',
      DEFAULT_BOUNDARY=       'HttpRequestDataMultipart',
      BOUNDARY_SEPARATOR=     '--';

    protected
      $parts      = array(),
      $boundary   = NULL;

    /**
     *
     */
    public function __construct($parts = array(), $boundary= NULL) {
      $this->addParts($parts);
      if(!empty($boundary)) {
        $this->boundary= $boundary;
      }
      // call with empty body since parts may be added later
      // so create data at the latest possible moment
      parent::__construct('');
    }

    /**
     * Returns the default type.
     *
     * @return string
     */
    protected function getDefaultType() {
      return self::DEFAULT_TYPE_MULTIPART;
    }

    /**
     * Will return the default content type header
     *
     * @return peer.Header
     */
    protected function getDefaultHeader_ContentType() {
      $defaultType= $this->getDefaultType();
      $boundary= $this->getBoundary();
      return HeaderFactory::getRequestHeader(HeaderFactory::TYPE_CONTENT_TYPE, $defaultType, NULL, $boundary);
    }

    /**
     * Add content part
     *
     * @param   peer.http.HttpRequestData part
     * @return  peer.http.HttpRequestData
     */
    public function addPart(HttpRequestData $part) {
      $this->parts[]= $part;
      return $part;
    }

    /**
     * Same as peer.http.HttpRequestDataMulti#addPart, but with more than one
     *
     * @param   {:peer.http.HttpRequestData] parts
     */
    public function addParts($parts= array()) {
      $parts= (array)$parts;
      foreach($parts as $part) {
        $this->addPart($part);
      }
    }

    /**
     * Add form part - fluent interface
     *
     * @param   peer.http.HttpRequestData part
     * @return  peer.http.HttpRequestDataMulti this
     */
    public function withPart(HttpRequestData $part) {
      $this->addPart($part);
      return $this;
    }

    /**
     * Same as peer.http.HttpRequestDataMulti#addParts - fluent interface
     *
     * @param   [:peer.http.HttpRequestData] parts
     * @return  peer.http.HttpRequestDataMulti this
     */
    public function withParts($parts) {
      $this->addParts($parts);
      return $this;
    }

    /**
     * Set boundary
     *
     * @param   string boundary
     * @throws  lang.IllegalArgumentException on empty boundary
     * @throws  lang.IllegalStateException on Content-Type header not found or found more than once
     */
    public function setBoundary($boundary) {
      if(empty($boundary)) {
        throw new IllegalArgumentException('Empty boundary given');
      }
      $this->boundary= $boundary;
      // change boundary in content type header
      $contentTypeHeader= $this->getHeadersForType(HttpFactory::TYPE_CONTENT_TYPE);
      if(is_array($contentTypeHeader) ||
         !$contentTypeHeader instanceof Header
      ) {
        throw new IllegalStateException('Content-Type Header set several times or not at all');
      }
      $contentTypeHeader->setBoundary($this->boundary);
    }

    /**
     * Set boundary - fluent interface
     *
     * @param   string boundary
     * @return  peer.http.HttpRequestDataMulti this
     * @throws  lang.IllegalArgumentException
     */
    public function withBoundary($boundary) {
      $this->setBoundary($boundary);
      return $this;
    }

    /**
     * Retrieve boundary.
     * If none set so far will generate one using the time.
     *
     * @return  string
     */
    public function getBoundary() {
      if(NULL === $this->boundary) {
        $this->boundary= uniqid(time());
      }
      return $this->boundary;
    }

    /**
     * Retrieve data for request
     *
     * @return  string
     */
    public function getData() {
      $ret= self::BOUNDARY_SEPARATOR.$this->getBoundary;
      foreach ($this->parts as $part) {
        $ret.=  HttpConstants::CRLF.$part->getData().HttpConstants::CRLF.self::BOUNDARY_SEPARATOR.$this->getBoundary;
      }
      return $ret.self::BOUNDARY_SEPARATOR.HttpConstants::CRLF;
    }
  }
?>
