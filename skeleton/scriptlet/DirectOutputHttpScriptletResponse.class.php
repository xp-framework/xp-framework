<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.HttpScriptletResponse');

  /**
   * A specialized child of HttpScriptletResponse that outputs
   * content directly onto the output stream in contrast to the base
   * implementation that caches all output.
   *
   * @see      xp://scriptlet.HttpScriptletResponse
   * @purpose  Response class specialized for huge amounts of data
   */
  class DirectOutputHttpScriptletResponse extends HttpScriptletResponse {

    /**
     * Set header, if still possible
     *
     * @throws  lang.IllegalStateException
     */
    public function setHeader($name, $value) {
      if ($this->headersSent()) throw new IllegalStateException(
        'Cannot set headers when headers have already been sent!'
      );

      parent::setHeader($name, $value);
    }

    /**
     * Sends content to STDOUT (which, on a webserver, is equivalent
     * to "send data to client").
     *
     */
    public function sendContent() {
      // NOOP
    }

    /**
     * Adds content to this response
     *
     * @param   string s string to add to the content
     */
    public function write($s) {
      echo $s;
    }
  }
?>