<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.Segment');

  /**
   * IPTC meta data segment
   * 
   */
  class IptcSegment extends Segment {
    protected $data= array();

    /**
     * Creates a segment instance
     *
     * @param string $marker
     * @param [:int] $data
     */
    public function __construct($marker, $data) {
      parent::__construct($marker, NULL);
      $this->data= $data;
    }

    /**
     * Returns the raw data
     * 
     * @return [:var]
     */
    public function rawData() {
      return $this->data;
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->marker.'>'.xp::stringOf($this->data);
    }

    /**
     * Test for equality
     *
     * @param  var cmp
     * @return bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $cmp->marker === $this->marker &&
        $cmp->data === $this->data
      );
    }
  }
?>