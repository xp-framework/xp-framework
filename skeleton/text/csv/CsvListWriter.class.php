<?php
/* This class is part of the XP framework
 *
 * $Id: CsvListWriter.class.php 11468 2009-09-15 09:50:52Z friebe $
 */

  uses('text.csv.CsvWriter');

  /**
   * Writes a list of values to CSV lines
   *
   * @test     xp://net.xp_framework.unittest.text.csv.CsvListWriterTest
   */
  class CsvListWriter extends CsvWriter {
    
    /**
     * Write a record
     *
     * @param   string[]
     */
    public function write(array $values) {
      $this->writeValues($values);
    }    
  }
?>
