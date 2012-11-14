<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Disposition header
   *
   * This Header is not part of HTTP/1.1
   *
   * @purpose
   */
  class ContentDispositionHeader extends AbstractContentHeader {

    const
      NAME= 'Content-Disposition',
      FORMAT= '%s; filename="%s"';

    protected
      $disposition= NULL,
      $filename=    NULL;

    /**
     * Create this header
     *
     * @param string disposition
     * @param string filename
     * @throws lang.IllegalArgumentException on empty filename
     */
    public function __construct($disposition, $filename= '') {
      $this->disposition= $disposition;
      if(!empty($filename)) {
        $this->setFilename($filename);
      }
      parent::__construct(self::NAME, '');
    }

    /**
     * Will create the correctly formated value with the given params
     *
     * @param string disposition
     * @param string filename
     * @return string
     */
    protected function prepareValue($disposition, $filename) {
      return sprintf(self::FORMAT, $disposition, $filename);;
    }

    /**
     * Will return the value of this Header
     *
     * @return string
     */
    public function getValue() {
      return $this->prepareValue($this->disposition, $this->filename);
    }

    /**
     * Set filename after initialization
     *
     * @param string filename
     * @throws lang.IllegalArgumentException on empty filename
     */
    public function setFilename($filename) {
      if(empty($filename)) {
        throw new IllegalArgumentException('Empty filename given');
      }
      $this->filename= $filename;
    }

    /**
     * Set filename after initialization - fluent interface
     *
     * @param string filename
     * @throws lang.IllegalArgumentException on empty filename
     */
    public function withFilename($filename) {
      $this->setFilename($filename);
      return $this;
    }

    /**
     * Is no request header
     *
     * @return bool FALSE
     */
    public function isRequestHeader() {
      return FALSE;
    }
  }
?>
