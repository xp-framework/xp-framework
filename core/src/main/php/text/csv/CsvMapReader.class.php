<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.csv.CsvReader');

  /**
   * Reads values from CSV lines into maps.
   *
   * @see      xp://text.csv.CsvListReader
   * @test     xp://net.xp_framework.unittest.text.csv.CsvMapReaderTest
   */
  class CsvMapReader extends CsvReader {
    protected $keys= array();

    /**
     * Creates a new CSV reader reading data from a given TextReader
     * creating Beans for a given class.
     *
     * @param   io.streams.TextReader reader
     * @param   string[] keys
     * @param   text.csv.CsvFormat format
     */
    public function  __construct(TextReader $reader, array $keys= array(), CsvFormat $format= NULL) {
      parent::__construct($reader, $format);
      $this->keys= $keys;
    }
    
    /**
     * Set keys
     *
     * @param   string[] keys
     */
    public function setKeys(array $keys) {
      $this->keys= $keys;
    }

    /**
     * Set keys
     *
     * @param   string[] keys
     * @return  text.csv.CsvMapReader this reader
     */
    public function withKeys(array $keys) {
      $this->keys= $keys;
      return $this;
    }

    /**
     * Get keys
     *
     * @return  string[] keys
     */
    public function getKeys() {
      return $this->keys;
    }

    /**
     * Read a record
     *
     * @return  [:var] or NULL if end of the file is reached
     */
    public function read() {
      if (NULL === ($values= $this->readValues())) return NULL;

      $map= array();
      $s= sizeof($values)- 1;
      foreach ($this->keys as $i => $key) {
        $map[$key]= $i > $s ? NULL : $values[$i];
      }
      return $map;
    }    
  }
?>
