<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.rdbms.integration.RdbmsIntegrationTest');

  /**
   * Sybase integration test
   *
   * @ext       sybase_ct
   */
  class SybaseIntegrationTest extends RdbmsIntegrationTest {

    /**
     * Before class method: set minimun server severity;
     * otherwise server messages end up on the error stack
     * and will let the test fail (no error policy).
     *
     */
    #[@beforeClass]
    public static function setMinimumServerSeverity() {
      if (function_exists('sybase_min_server_severity')) {
        sybase_min_server_severity(12);
      }
    }

    /**
     * Skip tests which require a specific minimum server version
     *
     */
    public function setUp() {
      parent::setUp();
      $m= $this->getClass()->getMethod($this->name);
      if ($m->hasAnnotation('version')) {
        $server= $this->db()->query('select @@version_number as v')->next('v');
        if ($server < ($required= $m->getAnnotation('version'))) {
          throw new PrerequisitesNotMetError('Server version not sufficient: '.$server, NULL, array($required));
        }
      }
    }    

    /**
     * Creates table name
     *
     * @return  string
     */
    protected function tableName() {
      return '#unittest';
    }

    /**
     * Retrieve dsn
     *
     * @return  string
     */
    public function _dsn() {
      return 'sybase';
    }
    
    /**
     * Create autoincrement table
     *
     * @param   string name
     */
    protected function createAutoIncrementTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk int identity, username varchar(30))', $name);
    }

    /**
     * Create transactions table
     *
     * @param   string name
     */
    protected function createTransactionsTable($name) {
      $this->removeTable($name);
      $this->db()->query('create table %c (pk int, username varchar(30))', $name);
    }

    /**
     * Test selecting string values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyString() {
      $this->assertEquals(' ', $this->db()->query('select "" as value')->next('value'));
    }

    /**
     * Test selecting string values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyVarchar() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as varchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting text values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyText() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as text) as value')->next('value'));
    }

    /**
     * Test selecting image values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyImage() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as image) as value')->next('value'));
    }

    /**
     * Test selecting binary values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyBinary() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as binary) as value')->next('value'));
    }

    /**
     * Test selecting varbinary values
     *
     * @see   http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
     */
    #[@test]
    public function selectEmptyVarBinary() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as varbinary) as value')->next('value'));
    }

    /**
     * Test selecting univarchar values
     *
     */
    #[@test]
    public function selectEmptyUniVarChar() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as univarchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUniVarChar() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as univarchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectUmlautUniVarChar() {
      $this->assertEquals('Übercoder', $this->db()->query('select cast("Übercoder" as univarchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test]
    public function selectNullUniVarChar() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as univarchar(255)) as value')->next('value'));
    }

    /**
     * Test selecting unitext values
     *
     */
    #[@test, @version(15000)]
    public function selectEmptyUniText() {
      $this->assertEquals(' ', $this->db()->query('select cast("" as unitext) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @version(15000)]
    public function selectUniText() {
      $this->assertEquals('test', $this->db()->query('select cast("test" as unitext) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @version(15000)]
    public function selectUmlautUniText() {
      $this->assertEquals('Übercoder', $this->db()->query('select cast("Übercoder" as unitext) as value')->next('value'));
    }

    /**
     * Test selecting char values
     *
     */
    #[@test, @version(15000)]
    public function selectNullUniText() {
      $this->assertEquals(NULL, $this->db()->query('select cast(NULL as unitext) as value')->next('value'));
    }

    /**
     * Test selecting an unsigned int
     *
     */
    #[@test, @version(15000)]
    public function selectUnsignedInt() {
      parent::selectUnsignedInt();
    }

    /**
     * Test selecting an unsigned bigint
     *
     */
    #[@test, @version(15000)]
    public function selectMaxUnsignedBigInt() {
      parent::selectMaxUnsignedBigInt();
    }

    /**
     * Test messages
     *
     */
    #[@test, @expect(class = 'rdbms.SQLStatementFailedException', withMessage= '/More power/'))]
    public function raiseError() {
      $this->db()->query('raiserror 61000 "More power"');
    }

    /**
     * Test messages
     *
     */
    #[@test]
    public function printMessage() {
      $q= $this->db()->query('
        print "More power"
        select 1 as "result"
      ');
      $this->assertEquals(1, $q->next('result'));
    }

    /**
     * Ensure an SQL exception is thrown for error 241
     *
     * @see  http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.dc00729_1500/html/errMessageAdvRes/BABIDFFD.htm
     * @see  https://github.com/xp-framework/xp-framework/issues/274
     */
    #[@test, @expect(class= 'rdbms.SQLStatementFailedException', withMessage= '/241/')]
    public function dataTruncationWarning() {
      $conn= $this->db();
      $conn->query('
        create table %c (
          id int primary key not null,
          cost numeric(10,4) not null
        )',
        $this->tableName()
      );
      $conn->insert('into %c (cost) values (123.12345)', $this->tableName());
    }
  }
?>
