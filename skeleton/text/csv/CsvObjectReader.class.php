<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.csv.CsvReader');

  /**
   * Reads values from CSV lines into objects.
   *
   * Example:
   * <code>
   *   class Person extends Object {
   *     protected $name= '';
   *   }
   *   
   *   // ...
   *   $beanreader->read(array('name'));
   * </code>
   *
   * The read creates a Person instance and sets its name property to
   * the value read.
   *
   * @see      xp://text.csv.CsvBeanReader
   * @test     xp://net.xp_framework.unittest.text.csv.CsvObjectReaderTest
   */
  class CsvObjectReader extends CsvReader {
    protected
      $class  = NULL;

    /**
     * Creates a new CSV reader reading data from a given TextReader
     * creating objects for a given class.
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
      
      // Create an object by deserialization. This enables us to also set
      // private and protected fields as well as avoids the constructor call.
      $n= xp::reflect($this->class->getName());
      $s= 'O:'.strlen($n).':"'.$n.'":'.sizeof($fields).':{';
      foreach ($fields as $i => $name) {
        $f= $this->class->getField($name);
        switch ($f->getModifiers() & (MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE)) {
          case MODIFIER_PUBLIC: $s.= serialize($f->getName()); break;
          case MODIFIER_PROTECTED: $s.= serialize("\0*\0".$f->getName()); break;
          case MODIFIER_PRIVATE: $s.= serialize("\0".$n."\0".$f->getName()); break;
        }
        $s.= serialize($values[$i]);
      }
      $s.= '}';
      return unserialize($s);
    }    
  }
?>
