<?php namespace xp\codegen;

use io\streams\StringWriter;

/**
 * Output to console
 */
class ConsoleOutput extends AbstractOutput {
  protected
    $writer = null;
    
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
