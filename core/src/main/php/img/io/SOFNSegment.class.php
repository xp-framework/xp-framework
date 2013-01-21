<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.Segment');

  /**
   * SOF[n] meta data segment
   * 
   */
  class SOFNSegment extends Segment {
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
     * Creates a segment instance
     *
     * @param  string $marker
     * @param  string $bytes
     * @return self
     */
    public static function read($marker, $bytes) {
      return new self($marker, unpack('Cbits/nheight/nwidth/Cchannels', $bytes));
    }

    /**
     * Get image bits
     *
     * @return int
     */
    public function bits() {
      return $this->data['bits'];
    }

    /**
     * Get image height
     *
     * @return int
     */
    public function height() {
      return $this->data['height'];
    }

    /**
     * Get image width
     *
     * @return int
     */
    public function width() {
      return $this->data['width'];
    }

    /**
     * Get image channels
     *
     * @return int
     */
    public function channels() {
      return $this->data['channels'];
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