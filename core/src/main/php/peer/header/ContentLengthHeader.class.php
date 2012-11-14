<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Length header
   * Instead of the length the content is required to be set.
   * The length will then be calculated automatically at the last possible moment
   *
   * @purpose
   */
  class ContentLengthHeader extends AbstractContentHeader {

    const
      NAME= 'Content-Length';

    /**
     * Create this header.
     * Requires the content instead of the value.
     * The value will get calculated once it is needed.
     *
     * @param string content
     */
    public function __construct($length= '') {
      parent::__construct(self::NAME, $length);
    }

    /**
     * Will return the given length or calculate the length if a content was given
     *
     * @return string
     */
    public function getValue() {
      // recalc on any call, since content might change
      if($this->hasContent()) {
        return strlen($this->getContent());
      }
      return parent::getValue();
    }
  }
?>
