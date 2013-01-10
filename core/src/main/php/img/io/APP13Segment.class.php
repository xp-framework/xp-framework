<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.Segment');

  /**
   * APP13 meta data segment
   * 
   */
  class APP13Segment extends Segment {

    /**
     * Creates a segment instance
     *
     * @param  string $marker
     * @param  string $bytes
     * @return self
     */
    public static function read($marker, $bytes) {
      if (is_array($iptc= iptcparse($bytes))) {
        return XPClass::forName('img.io.IptcSegment')->newInstance($marker, $iptc);
      } else {
        return new self($marker, $bytes);
      }
    }
  }
?>