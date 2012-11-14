<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Location header
   *
   * @purpose
   */
  class ContentLocationHeader extends AbstractContentHeader {

    const
      NAME= 'Content-Location';

    /**
     * Create this header
     *
     * @param string location
     */
    public function __construct($location) {
      parent::__construct(self::NAME, $location);
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
