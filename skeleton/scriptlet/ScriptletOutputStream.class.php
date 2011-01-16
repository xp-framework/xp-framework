<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream');

  /**
   * Scriptlet output stream. Buffers data until flushed.
   *
   */
  class ScriptletOutputStream extends Object implements OutputStream {
    protected $res= NULL;
    protected $buffer= '';
    protected $committed= FALSE;

    /**
     * Constructor
     *
     * @param   scriptlet.Response res
     */
    public function __construct(Response $res) {
      $this->res= $res;
    }
    
    /**
     * Writes data to this outputstream
     *
     * @param   string data
     */
    public function write($data) {
      if ($this->committed) {
        $this->res->writeBytes($data);
      } else {
        $this->buffer.= $data;
      }
    }
    
    /**
     * Flushes this outputstream
     *
     */
    public function flush() {
      if (!$this->committed) {
        $this->res->writeHeaders($this->out);
        $this->res->writeBytes($this->buffer);
        $this->committed= TRUE;
      }
    }
    
    /**
     * Closes this outputstream
     *
     */
    public function close() {
      $this->flush();
    }
    
    /**
     * Resets this outputstream
     *
     */
    public function reset() {
      if ($this->committed) {
        throw new IllegalStateException('Cannot reset committed buffer');
      }
      $this->buffer= '';
    }

    /**
     * Returns whether the response has been comitted yet.
     *
     * @return  bool
     */
    public function isCommitted() {
      return $this->committed;
    }
  }
?>
