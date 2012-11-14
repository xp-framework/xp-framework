<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.header.AbstractContentHeader'
  );

  /**
   * Represents a Content Range header
   *
   * @purpose
   */
  class ContentRangeHeader extends AbstractContentHeader {

    const
      NAME=   'Content-Range',
      FORMAT= 'bytes %d-%d/%d';

    /**
     * Create this header
     *
     * @param int start
     * @param int end
     * @param int max
     */
    public function __construct($start, $end, $max) {
      $value= $this->prepareValue($start, $end, $max);
      parent::__construct(self::NAME, $value);
    }

    /**
     * Will create the correctly formated value with the given params
     *
     * @param int start
     * @param int end
     * @param int max
     * @return string
     */
    protected function prepareValue($start, $end, $max) {
      $value= sprintf(self::FORMAT, $start, $end, $max);
      return $value;
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
