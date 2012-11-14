<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content MD5 header
   * The value of the header will be determined, at the last possible moment
   * Therefore, instead of the value to be send, the content itself is required as parameter
   * Upon this content the md5 is later generated.
   *
   * @purpose
   */
  class ContentMD5Header extends AbstractContentHeader {

    const
      NAME=   'Content-MD5';

    /**
     * Create this header.
     * The value will get calculated once it is needed.
     */
    public function __construct($md5= '') {
      parent::__construct(self::NAME, $md5);
    }

    /**
     * Will create the base 64 encoded md5 hash of the set content
     * and return it if a content was given. Otherwise the set value is used.
     *
     * @return string
     */
    public function getValue() {
      // recalc on any call, since content might change
      if($this->hasContent()) {
        return base64_encode(md5($this->getContent()));
      }
      return parent::getValue();
    }
  }
?>
