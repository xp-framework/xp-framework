<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Image meta data segment
   * 
   */
  class Segment extends Object {
    public $marker= NULL;
    public $bytes= NULL;

    /**
     * Creates a segment instance
     *
     * @param string $marker
     * @param string $bytes
     */
    public function __construct($marker, $bytes) {
      $this->marker= $marker;
      $this->bytes= $bytes;
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->marker.'>('.strlen($this->bytes).' bytes)';
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
        $cmp->bytes === $this->bytes
      );
    }
  }
?>