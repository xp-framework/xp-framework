<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xml.parser.InputSource');

  /**
   * Input source
   *
   * @see      xp://xml.parser.XMLParser#parse
   */
  class StreamInputSource extends Object implements InputSource {
    protected
      $stream = NULL,
      $source = '';
   
    /**
     * Constructor.
     *
     * @param   io.streams.InputStream input
     * @param   string source
     */
    public function __construct(InputStream $input, $source= '(stream)') {
      $this->stream= $input;
      $this->source= $source;
    }

    /**
     * Get stream
     *
     * @return  io.streams.InputStream
     */
    public function getStream() {
      return $this->stream;
    }

    /**
     * Get source
     *
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

    /**
     * Creates a string representation of this InputSource
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->source.'>';
    }
  }
?>
