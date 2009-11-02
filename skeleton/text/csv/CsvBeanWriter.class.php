<?php
/* This class is part of the XP framework
 *
 * $Id: CsvBeanWriter.class.php 11481 2009-09-15 10:45:51Z friebe $
 */

  uses('text.csv.CsvWriter');

  /**
   * Writes beans to CSV lines
   *
   * @test     xp://net.xp_framework.unittest.text.csv.CsvBeansWriterTest
   */
  class CsvBeanWriter extends CsvWriter {
    
    /**
     * Write a record
     *
     * @param   lang.Generic object
     * @param   string[] fields if omitted, all fields will be written
     */
    public function write(Generic $object, array $fields= array()) {
      $values= array();
      $class= $object->getClass();
      if (!$fields) {
        foreach ($class->getFields() as $f) {
          $values[]= $class->getMethod('get'.ucfirst($f->getName()))->invoke($object);
        }
      } else {
        foreach ($fields as $name) {
          $values[]= $class->getMethod('get'.ucfirst($name))->invoke($object);
        }
      }
      return $this->writeValues($values);
    }
  }
?>
