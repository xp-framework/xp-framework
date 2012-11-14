<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Langauge header
   *
   * @purpose
   */
  class ContentLanguageHeader extends AbstractContentHeader {

    const
      NAME= 'Content-Language';

    /**
     * Create this header
     *
     * @param string language
     */
    public function __construct($language) {
      parent::__construct(self::NAME, $language);
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
