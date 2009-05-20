<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.InputStream');

  /**
   * Input source
   *
   * @see      xp://xml.parser.XMLParser#parse
   */
  interface InputSource {

    /**
     * Get stream
     *
     * @return  io.streams.InputStream
     */
    public function getStream();

    /**
     * Get source
     *
     * @return  string
     */
    public function getSource();
  }
?>
