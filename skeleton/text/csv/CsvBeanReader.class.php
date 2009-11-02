<?php
/* This class is part of the XP framework
 *
 * $Id: CsvBeanReader.class.php 11477 2009-09-15 10:28:46Z friebe $
 */

  uses('text.csv.CsvReader');

  /**
   * Reads values from CSV lines into Beans. Works like the object reader
   * but instead of directly accessing the properties uses setter methods.
   *
   * Example:
   * <code>
   *   class Person extends Object {
   *     protected $name= '';
   *
   *     public function setName($name) { $this->name= $name; }
   *     public function getName() { return $this->name; }
   *   }
   *   
   *   // ...
   *   $beanreader->read(array('name'));
   * </code>
   *
   * The read creates a Person instance and invokes its setName() method
   * with the value read.
   *
   * @see      xp://text.csv.CsvObjectReader
   * @test     xp://net.xp_framework.unittest.text.csv.CsvBeanReaderTest
   */
  class CsvBeanReader extends CsvReader {

    /**
     * Creates a new CSV reader reading data from a given TextReader
     * creating Beans for a given class.
     *
     * @param   io.streams.TextReader reader
     * @param   lang.XPClass class
     * @param   text.csv.CsvFormat format
     */
    public function  __construct(TextReader $reader, XPClass $class, CsvFormat $format= NULL) {
      parent::__construct($reader, $format);
      $this->class= $class;
    }
    
    /**
     * Read a record
     *
     * @param   string[] fields if omitted, class fields are used in order of appearance
     * @return  lang.Object or NULL if end of the file is reached
     */
    public function read(array $fields= array()) {
      if (NULL === ($values= $this->readValues())) return NULL;

      if (!$fields) foreach ($this->class->getFields() as $f) {
        $fields[]= $f->getName();
      }
      
      $instance= $this->class->newInstance();
      foreach ($fields as $i => $name) {
        $this->class->getMethod('set'.ucfirst($name))->invoke($instance, array($values[$i]));
      }
      return $instance;
    }    
  }
?>
