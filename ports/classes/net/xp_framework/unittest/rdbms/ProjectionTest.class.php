<?php
/* This class is part of the XP framework
 *
 * $Id: DBXmlGeneratorTest.class.php 9200 2007-01-08 21:55:03Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'rdbms.sybase.SybaseConnection',
    'rdbms.mysql.MySQLConnection',
    'rdbms.pgsql.PostgreSQLConnection',
    'rdbms.sqlite.SQLiteConnection',
    'rdbms.criterion.Restrictions',
    'rdbms.criterion.Projections',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * TestCase
   *
   * @see      rdbms.criterion.Projections
   * @purpose  Unit Tests
   */
  class ProjectionTest extends TestCase {
    public
      $syconn = NULL,
      $myconn = NULL,
      $pgconn = NULL,
      $sqconn = NULL,
      $peer   = NULL;
      
    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $this->syconn= new SybaseConnection(new DSN('sybase://localhost:1999/'));
      $this->myconn= new MySQLConnection(new DSN('mysql://localhost/'));
      $this->pgconn= new PostgreSQLConnection(new DSN('pgsql://localhost/'));
      $this->sqconn= new SqliteConnection(new DSN('sqlite://tmpdir/tmpdb'));
      $this->peer= Job::getPeer();
    }
    
    /**
     * Helper method that will call toSQL() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string mysql
     * @param   string sysql
     * @param   string pgsql
     * @param   string sqlite
     * @param   rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSql($mysql, $sysql, $pgsql, $sqlite, $criteria) {
      $this->assertEquals('mysql: '.$mysql,  'mysql: '.trim($criteria->toSQL($this->myconn, $this->peer), ' '));
      $this->assertEquals('sybase: '.$sysql, 'sybase: '.trim($criteria->toSQL($this->syconn, $this->peer), ' '));
      $this->assertEquals('pgsql: '.$pgsql, 'pgsql: '.trim($criteria->toSQL($this->pgconn, $this->peer), ' '));
      $this->assertEquals('sqlite: '.$sqlite, 'sqlite: '.trim($criteria->toSQL($this->sqconn, $this->peer), ' '));
    }
    
    /**
     * Helper method that will call projection() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string mysql
     * @param   string sysql
     * @param   string pgsql
     * @param   string sqlite
     * @param   rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertProjection($mysql, $sysql, $pgsql, $sqlite, $criteria) {
      $this->assertEquals('mysql: '.$mysql,  'mysql: '.trim($criteria->projections($this->myconn, $this->peer), ' '));
      $this->assertEquals('sybase: '.$sysql, 'sybase: '.trim($criteria->projections($this->syconn, $this->peer), ' '));
      $this->assertEquals('pgsql: '.$pgsql, 'pgsql: '.trim($criteria->projections($this->pgconn, $this->peer), ' '));
      $this->assertEquals('sqlite: '.$sqlite, 'sqlite: '.trim($criteria->projections($this->sqconn, $this->peer), ' '));
    }
    
    /**
     * test the count projection
     *
     */
    #[@test]
    function countTest() {
      $this->assertProjection(
        'count(*) as `count`',
        'count(*) as \'count\'',
        'count(*) as "count"',
        'count(*) as \'count\'',
        create(new Criteria())->setProjection(Projections::count())
      );
    }

    /**
     * test the count projection with a parameter
     *
     */
    #[@test]
    function countColumnTest() {
      $this->assertProjection(
        'count(job_id) as `count_job_id`',
        'count(job_id) as \'count_job_id\'',
        'count(job_id) as "count_job_id"',
        'count(job_id) as \'count_job_id\'',
        create(new Criteria())->setProjection(Projections::count(Job::column('job_id')), 'count_job_id')
      );
    }

    /**
     * test the count projection with a parameter and an alias
     *
     */
    #[@test]
    function countColumnAliasTest() {
      $this->assertProjection(
        'count(job_id) as `counting all`',
        'count(job_id) as \'counting all\'',
        'count(job_id) as "counting all"',
        'count(job_id) as \'counting all\'',
        create(new Criteria())->setProjection(Projections::count(Job::column('job_id')), "counting all")
      );
    }

    /**
     * test the count projection with an alias
     *
     */
    #[@test]
    function countAliasTest() {
      $this->assertProjection(
        'count(*) as `counting all`',
        'count(*) as \'counting all\'',
        'count(*) as "counting all"',
        'count(*) as \'counting all\'',
        create(new Criteria())->setProjection(Projections::count('*'), "counting all")
      );
    }

    /**
     * test the average projection
     *
     */
    #[@test]
    function avgTest() {
      $this->assertProjection(
        'avg(job_id)',
        'avg(job_id)',
        'avg(job_id)',
        'avg(job_id)',
        create(new Criteria())->setProjection(Projections::average(Job::column("job_id")))
      );
    }

    /**
     * test the sum projection
     *
     */
    #[@test]
    function sumTest() {
      $this->assertProjection(
        'sum(job_id)',
        'sum(job_id)',
        'sum(job_id)',
        'sum(job_id)',
        create(new Criteria())->setProjection(Projections::sum(Job::column("job_id")))
      );
    }

    /**
     * test the min projection
     *
     */
    #[@test]
    function minTest() {
      $this->assertProjection(
        'min(job_id)',
        'min(job_id)',
        'min(job_id)',
        'min(job_id)',
        create(new Criteria())->setProjection(Projections::min(Job::column("job_id")))
      );
    }

    /**
     * test the max projection
     *
     */
    #[@test]
    function maxTest() {
      $this->assertProjection(
        'max(job_id)',
        'max(job_id)',
        'max(job_id)',
        'max(job_id)',
        create(new Criteria())->setProjection(Projections::max(Job::column("job_id")))
      );
    }

    /**
     * test the property projection
     *
     */
    #[@test]
    function propertyTest() {
      $this->assertProjection(
        'job_id',
        'job_id',
        'job_id',
        'job_id',
        create(new Criteria())->setProjection(Projections::property(Job::column("job_id")))
      );
    }

    /**
     * test the projection list projection
     *
     */
    #[@test]
    function propertyListTest() {
      $this->assertProjection(
        'job_id, title',
        'job_id, title',
        'job_id, title',
        'job_id, title',
        create(new Criteria())->setProjection(Projections::projectionList()
          ->add(Projections::property(Job::column('job_id')))
          ->add(Projections::property(Job::column('title')))
      ));
      $this->assertClass(
        Projections::projectionList()->add(Projections::property(Job::column('job_id'))),
        'rdbms.criterion.ProjectionList'
      );
    }

    /**
     * test the projection list projection with aliases
     *
     */
    #[@test]
    function propertyListAliasTest() {
      $this->assertProjection(
        'job_id as `id`, title',
        'job_id as \'id\', title',
        'job_id as "id", title',
        'job_id as \'id\', title',
        create(new Criteria())->setProjection(Projections::projectionList()
          ->add(Projections::property(Job::column('job_id')), 'id')
          ->add(Job::column('title'))
      ));
    }

    /**
     * test to set and unset projections
     *
     */
    #[@test]
    function setProjectionTest() {
      $crit= new Criteria();
      $this->assertFalse($crit->isProjection());
      $crit->setProjection(Projections::property(Job::column('job_id')));
      $this->assertTrue($crit->isProjection());
      $crit->setProjection(NULL);
      $this->assertFalse($crit->isProjection());
      $crit->setProjection(Job::column('job_id'));
      $this->assertTrue($crit->isProjection());
      $crit->setProjection();
      $this->assertFalse($crit->isProjection());
    }

    /**
     * test temporarly set projections
     *
     */
    #[@test]
    function withProjectionTest() {
      $crit= new Criteria();
      $this->assertClass(
        $crit->withProjection(Projections::property(Job::column('job_id'))),
        'rdbms.Criteria'
      );
      $this->assertFalse($crit->isProjection());
      $this->assertTrue($crit->withProjection(Projections::property(Job::column('job_id')))->isProjection());
    }

  }
?>
