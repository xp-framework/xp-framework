<?php namespace net\xp_framework\unittest\rdbms\integration;

/**
 * Sybase integration test
 *
 * @see  http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.help.ase_15.0.blocks/html/blocks/blocks258.htm
 * @see  http://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.dc00729_1500/html/errMessageAdvRes/BABIDFFD.htm
 * @see  https://github.com/xp-framework/xp-framework/issues/274
 * @ext  sybase_ct
 */
class SybaseIntegrationTest extends RdbmsIntegrationTest {

  /**
   * Before class method: set minimun server severity;
   * otherwise server messages end up on the error stack
   * and will let the test fail (no error policy).
   */
  #[@beforeClass]
  public static function setMinimumServerSeverity() {
    if (function_exists('sybase_min_server_severity')) {
      sybase_min_server_severity(12);
    }
  }

  /**
   * Skip tests which require a specific minimum server version
   */
  public function setUp() {
    parent::setUp();
    $m= $this->getClass()->getMethod($this->name);
    if ($m->hasAnnotation('version')) {
      $server= $this->db()->query('select @@version_number as v')->next('v');
      if ($server < ($required= $m->getAnnotation('version'))) {
        throw new \unittest\PrerequisitesNotMetError('Server version not sufficient: '.$server, null, array($required));
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

  #[@test]
  public function selectEmptyString() {
    $this->assertEquals(' ', $this->db()->query('select "" as value')->next('value'));
  }

  #[@test]
  public function selectEmptyVarchar() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as varchar(255)) as value')->next('value'));
  }

  #[@test]
  public function selectEmptyText() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as text) as value')->next('value'));
  }

  #[@test]
  public function selectEmptyImage() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as image) as value')->next('value'));
  }

  #[@test]
  public function selectEmptyBinary() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as binary) as value')->next('value'));
  }

  #[@test]
  public function selectEmptyVarBinary() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as varbinary) as value')->next('value'));
  }

  #[@test]
  public function selectEmptyUniVarChar() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as univarchar(255)) as value')->next('value'));
  }

  #[@test]
  public function selectUniVarChar() {
    $this->assertEquals('test', $this->db()->query('select cast("test" as univarchar(255)) as value')->next('value'));
  }

  #[@test]
  public function selectUmlautUniVarChar() {
    $this->assertEquals('Übercoder', $this->db()->query('select cast("Übercoder" as univarchar(255)) as value')->next('value'));
  }

  #[@test]
  public function selectNullUniVarChar() {
    $this->assertEquals(null, $this->db()->query('select cast(NULL as univarchar(255)) as value')->next('value'));
  }

  #[@test, @version(15000)]
  public function selectEmptyUniText() {
    $this->assertEquals(' ', $this->db()->query('select cast("" as unitext) as value')->next('value'));
  }

  #[@test, @version(15000)]
  public function selectUniText() {
    $this->assertEquals('test', $this->db()->query('select cast("test" as unitext) as value')->next('value'));
  }

  #[@test, @version(15000)]
  public function selectUmlautUniText() {
    $this->assertEquals('Übercoder', $this->db()->query('select cast("Übercoder" as unitext) as value')->next('value'));
  }

  #[@test, @version(15000)]
  public function selectNullUniText() {
    $this->assertEquals(null, $this->db()->query('select cast(NULL as unitext) as value')->next('value'));
  }

  #[@test, @version(15000)]
  public function selectUnsignedInt() {
    parent::selectUnsignedInt();
  }

  #[@test, @version(15000)]
  public function selectMaxUnsignedBigInt() {
    parent::selectMaxUnsignedBigInt();
  }

  #[@test, @expect(class = 'rdbms.SQLStatementFailedException', withMessage= '/More power/')]
  public function raiseError() {
    $this->db()->query('raiserror 61000 "More power"');
  }

  #[@test]
  public function printMessage() {
    $q= $this->db()->query('
      print "More power"
      select 1 as "result"
    ');
    $this->assertEquals(1, $q->next('result'));
  }

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
