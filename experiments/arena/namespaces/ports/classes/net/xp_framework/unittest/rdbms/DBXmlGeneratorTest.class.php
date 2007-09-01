<?php
/* This class is part of the XP framework
 *
 * $Id: DBXmlGeneratorTest.class.php 10983 2007-08-28 11:12:32Z friebe $ 
 */

  namespace net::xp_framework::unittest::rdbms;

  ::uses(
    'unittest.TestCase',
    'rdbms.DBTable',
    'rdbms.DriverManager',
    'xml.XPath', 
    'rdbms.util.DBXmlGenerator',
    'rdbms.DBIndex'
  );

  /**
   * TestCase
   *
   * @see      rdbms.util.DBXmlGenerator
   * @purpose  Unit Tests
   */
  class DBXmlGeneratorTest extends unittest::TestCase {
    protected
      $xpath= NULL;

    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $generated= rdbms::util::DBXmlGenerator::createFromTable(
        $this->newTable('deviceinfo', array(
          'deviceinfo_id' => array(DB_ATTRTYPE_INT, 255), 
          'serial_number' => array(DB_ATTRTYPE_INT, 16),
          'text'          => array(DB_ATTRTYPE_TEXT, 255)
        )),
        'localhost',
        'FOOBAR'
      );
      $this->xpath= new xml::XPath($generated->getSource());
    }

    /**
     * Helper method which creates a database table
     *
     * @param string name, attr[]
     * @return  t DBTable object
     */
    public function newTable($name, $attr) {
      $t= new rdbms::DBTable($name);
      foreach ($attr as $key => $definitions) {
        $t->attributes[]= new DBTableAttribute(
          $key,
          $definitions[0],    // Type
          TRUE,
          FALSE,
          $definitions[1]     // Length
        );
      }
      $t->indexes[]= new rdbms::DBIndex(
        'PRIMARY',
        array('deviceinfo_id')
      );
      $t->indexes[0]->unique= TRUE;
      $t->indexes[0]->primary= TRUE;
      $t->indexes[]= new rdbms::DBIndex(
        'deviceinfo_I_serial',
        array('serial_number')
      );
      return $t;
    }
    

    /**
     * Checks if table name is correct.
     *
     */
    #[@test]
    public function correctTableNameSet() {
      $this->assertEquals('deviceinfo', $this->xpath->query('string(/document/table/@name)'));
    }

    /**
     * Checks if table name is correct.
     *
     */
    #[@test]
    public function correctDatabaseNameSet() {
      $this->assertEquals('FOOBAR', $this->xpath->query('string(/document/table/@database)'));
    }

    /**
     * Checks whether correct type of a field is set
     *
     */
    #[@test]
    public function correctTypeSet() {
      $this->assertEquals('DB_ATTRTYPE_TEXT', 
        $this->xpath->query('string(/document/table/attribute[3]/@type)'));
    }    

    /**
     * Checks whether correct type of a field is set
     *
     */
    #[@test]
    public function correctTypeNameSet() {
      $this->assertEquals('string', 
        $this->xpath->query('string(/document/table/attribute[3]/@typename)'));
      $this->assertEquals('int', 
        $this->xpath->query('string(/document/table/attribute[2]/@typename)'));
    }    

    /**
     * Checks whether primary key is set
     *
     */
    #[@test]
    public function primaryKeySet() {
      $this->assertEquals('true', 
        $this->xpath->query('string(/document/table/index[1]/@primary)'));
    }

    /**
     * Checks whether primary key is NOT set
     *
     */
    #[@test]
    public function primaryKeyNotSet() {
      $this->assertEquals('false', 
        $this->xpath->query('string(/document/table/index[2]/@primary)'));
    }    

    /**
     * Checks whether primary key is NOT set
     *
     */
    #[@test]
    public function uniqueKeySet() {
      $this->assertEquals('true', 
        $this->xpath->query('string(/document/table/index[1]/@unique)'));
    }

    /**
     * Checks whether primary key is NOT set
     *
     */
    #[@test]
    public function uniqueKeyNotSet() {
      $this->assertEquals('false', 
        $this->xpath->query('string(/document/table/index[2]/@unique)'));
    }
    
    /**
     * Checks key
     *
     */
    #[@test]
    public function correctKey() {
      $this->assertEquals('deviceinfo_id', 
        trim($this->xpath->query('string(/document/table/index[1]/key)')));
    }

    /**
     * Checks key
     *
     */
    #[@test]
    public function correctKeyName() {
      $this->assertEquals('PRIMARY', 
        $this->xpath->query('string(/document/table/index[1]/@name)'));
    }

    /**
     * Checks whether identity is set correctly
     *
     */
    #[@test]
    public function identitySet() {
      $this->assertEquals('true', 
        $this->xpath->query('string(/document/table/attribute[1]/@identity)'));
    }

    /**
     * Checks whether nullable is set correctly
     *
     */
    #[@test]
    public function nullableSet() {
      $this->assertEquals('false', 
        $this->xpath->query('string(/document/table/attribute[1]/@nullable)'));
    }

    /**
     * Checks whether dbhost is set correctly
     *
     */
    #[@test]
    public function dbhostSet() {
      $this->assertEquals('localhost', 
        $this->xpath->query('string(/document/table/@dbhost)'));
    }
  }
?>
