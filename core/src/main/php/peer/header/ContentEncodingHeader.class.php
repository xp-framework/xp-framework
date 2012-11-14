<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Encoding header
   *
   * @purpose
   */
  class ContentEncodingHeader extends AbstractContentHeader {

    const
      NAME= 'Content-Encoding';

    /**
     * Create this header
     *
     * @param string encoding
     */
    public function __construct($encoding) {
      parent::__construct(self::NAME, $encoding);
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
