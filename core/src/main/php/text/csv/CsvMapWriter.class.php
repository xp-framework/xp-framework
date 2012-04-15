<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.csv.CsvWriter');

  /**
   * Writes maps to CSV lines
   *
   * @test     xp://net.xp_framework.unittest.text.csv.CsvMapWriterTest
   */
  class CsvMapWriter extends CsvWriter {
    protected $keys= NULL;

    /**
     * Set header line
     *
     * @param   string[] headers
     * @throws  lang.IllegalStateException if writing has already started
     */
    public function setHeaders($headers) {
      parent::setHeaders($headers);
      $this->keys= $headers;
    }
    
    /**
     * Write a record
     *
     * @param   lang.Generic object
     * @param   string[] fields if omitted, all fields will be written
     */
    public function write(array $map) {
      $values= array();
      if (NULL === $this->keys) {
        foreach ($map as $key => $value) {
          $values[]= $value;
        }
      } else {
        foreach ($this->keys as $key) {
          $values[]= isset($map[$key]) ? $map[$key] : NULL;
        }
      }
      return $this->writeValues($values);
    }
  }
?>
