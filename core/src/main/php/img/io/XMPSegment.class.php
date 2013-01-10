<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.Segment', 'xml.dom.Document');

  /**
   * XMP/XAP meta data segment
   * 
   */
  class XMPSegment extends Segment {
    protected $document= NULL;

    /**
     * Creates a segment instance
     *
     * @param string $marker
     * @param xml.dom.Document document
     */
    public function __construct($marker, Document $document) {
      parent::__construct($marker, NULL);
      $this->document= $document;
    }

    /**
     * Creates a segment instance
     *
     * @param  string $marker
     * @param  string $bytes
     * @return self
     */
    public static function read($marker, $bytes) {

      // Begin parsing after 29 bytes - strlen("http://ns.adobe.com/xap/1.0/\000")
      return new self($marker, Document::fromString(substr($bytes, 29)));
    }

    /**
     * Get image bits
     *
     * @return int
     */
    public function document() {
      return $this->document;
    }

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->marker.'>'.xp::stringOf($this->document);
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
        $cmp->document->equals($this->document)
      );
    }
  }
?>