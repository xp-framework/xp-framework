<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Type header
   *
   * @purpose
   */
  class ContentTypeHeader extends AbstractContentHeader {

    const
      NAME=             'Content-Type',
      FORMAT=           '%s%s%s',
      FORMAT_CHARSET=   '; charset=%s',
      FORMAT_BOUNDARY=  '; boundary=%s';

    protected
      $type=      NULL,
      $charset=   NULL,
      $boundary=  NULL;

    /**
     * Create this header
     *
     * @param string type
     * @param string charset
     * @param string boundary
     */
    public function __construct($type, $charset= NULL, $boundary= NULL) {
      $this->setType($type);
      if(!empty($charset)) {
        $this->setCharset($charset);
      }
      if(!empty($boundary)) {
        $this->setBoundary($boundary);
      }
      parent::__construct(self::NAME, '');
    }

    /**
     * Will create the correctly formated value with the given params
     *
     * @param string type
     * @param string charset
     * @param string boundary
     * @return string
     */
    protected function prepareValue($type, $charset= NULL, $boundary= NULL) {
      $charsetPart=   $this->getPart(self::FORMAT_CHARSET, $charset);
      $boundaryPart=  $this->getPart(self::FORMAT_BOUNDARY, $boundary);
      return sprintf(self::FORMAT, $type, $charsetPart, $boundaryPart);
    }

    /**
     * Will return the value of this Header
     *
     * @return string
     */
    public function getValue() {
      return $this->prepareValue($this->type, $this->charset, $this->boundary);
    }

    /**
     * Set type after initialization
     *
     * @param string type
     * @throws lang.IllegalArgumentException on empty type
     */
    public function setType($type) {
      if(empty($type)) {
        throw new IllegalArgumentException('Empty type given');
      }
      $this->type= $type;
    }

    /**
     * Set type after initialization - fluent interface
     *
     * @param string type
     * @throws lang.IllegalArgumentException on empty type
     */
    public function withType($type) {
      $this->setType($type);
      return $this;
    }

    /**
     * Set charset after initialization
     *
     * @param string charset
     * @throws lang.IllegalArgumentException on empty charset
     */
    public function setCharset($charset) {
      if(empty($charset)) {
        throw new IllegalArgumentException('Empty charset given');
      }
      $this->charset= $charset;
    }

    /**
     * Set charset after initialization - fluent interface
     *
     * @param string charset
     * @throws lang.IllegalArgumentException on empty charset
     */
    public function withCharset($charset) {
      $this->setCharset($charset);
      return $this;
    }

    /**
     * Set boundary after initialization
     *
     * @param string boundary
     * @throws lang.IllegalArgumentException on empty boundary
     */
    public function setBoundary($boundary) {
      if(empty($boundary)) {
        throw new IllegalArgumentException('Empty boundary given');
      }
      $this->boundary= $boundary;
    }

    /**
     * Set boundary after initialization - fluent interface
     *
     * @param string boundary
     * @throws lang.IllegalArgumentException on empty boundary
     */
    public function withBoundary($boundary) {
      $this->setBoundary($boundary);
      return $this;
    }
  }
?>
