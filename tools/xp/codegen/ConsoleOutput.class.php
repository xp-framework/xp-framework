<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.codegen.AbstractOutput', 'io.streams.StringWriter');

  /**
   * Output to console
   *
   * @purpose  AbstractOutput implementation
   */
  class ConsoleOutput extends AbstractOutput {
    protected
      $writer = NULL;
      
    /**
     * Constructor
     *
     * @param   io.streams.StringWriter writer
     */
    public function __construct(StringWriter $writer) {
      $this->writer= $writer;
    }

    /**
     * Store data
     *
     * @param   string name
     * @param   string data
     */
    protected function store($name, $data) {
      $this->writer->writeLine($data);
    }

    
    /**
     * Commit output
     *
     */
    public function commit() {
      // NOOP
    }
  }
?>
