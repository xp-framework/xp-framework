<?php
/* This class is part of the XP framework
 *
 * $Id: DBXmlGeneratorTest.class.php 9200 2007-01-08 21:55:03Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'rdbms.ConnectionManager',
    'rdbms.criterion.Restrictions',
    'rdbms.SQLFunctions',
    'net.xp_framework.unittest.rdbms.dataset.Job'
  );

  /**
   * TestCase
   *
   * @see      rdbms.SQLFunction
   * @purpose  Unit Tests
   */
  class SQLFunctionTest extends TestCase {
    public
      $syconn = NULL,
      $myconn = NULL,
      $peer   = NULL;
      
    /**
     * Sets up a Database Object for the test
     *
     */
    public function setUp() {
      $this->syconn= DriverManager::getConnection('sybase://localhost:1999/');
      $this->myconn= DriverManager::getConnection('mysql://localhost:3306/');
      $this->peer= Job::getPeer();
    }
    
    /**
     * Helper method that will call toSQL() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string mysql
     * @param   string sysql
     * @param   rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertSql($mysql, $sysql, $criteria) {
      $this->assertEquals("mysql: ".$mysql,  "mysql: ".trim($criteria->toSQL($this->myconn, $this->peer), ' '));
      $this->assertEquals("sybase: ".$sysql, "sybase: ".trim($criteria->toSQL($this->syconn, $this->peer), ' '));
    }
    
    /**
     * Helper method that will call projection() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @param   string mysql
     * @param   string sysql
     * @param   rdbms.Criteria criteria
     * @throws  unittest.AssertionFailedError
     */
    protected function assertProjection($mysql, $sysql, $criteria) {
      $this->assertEquals("mysql: ".$mysql,  "mysql: ".trim($criteria->projections($this->myconn, $this->peer), ' '));
      $this->assertEquals("sybase: ".$sysql, "sybase: ".trim($criteria->projections($this->syconn, $this->peer), ' '));
    }
    
    /**
     * test the function set
     *
     */
    #[@test]
    function columnTest() {
      $this->assertEquals(
        'job_id',
        Job::column('job_id')->asSql($this->syconn)
      );
    }

    /**
     * test the function set
     *
     */
    #[@test]
    function projectionTest() {
      $this->assertProjection(
        'day(valid_from)',
        'day(valid_from)',
        create(new Criteria())->setProjection(SQLFunctions::day(Job::column('valid_from')))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function prepareProjectionTest() {
      $this->assertEquals(
        '- datepart(hour, valid_from) -',
        $this->syconn->prepare('- %s -', SQLFunctions::datepart('hour', Job::column('valid_from')))
      );
      $this->assertEquals(
        '- extract(hour from valid_from) -',
        $this->myconn->prepare('- %s -', SQLFunctions::datepart('hour', Job::column('valid_from')))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function stringFunctionTest() {
      $this->assertProjection(
        'ascii("a") as "asciiTest"',
        'ascii("a") as "asciiTest"',
        create(new Criteria())->setProjection(SQLFunctions::ascii("a"), 'asciiTest')
      );
      $this->assertProjection(
        'char(97)',
        'char(97)',
        create(new Criteria())->setProjection(SQLFunctions::char('97'))
      );
      $this->assertProjection(
        'length("aaaaaaa")',
        'len("aaaaaaa")',
        create(new Criteria())->setProjection(SQLFunctions::len("aaaaaaa"))
      );
      $this->assertProjection(
        'reverse("abcdefg")',
        'reverse("abcdefg")',
        create(new Criteria())->setProjection(SQLFunctions::reverse("abcdefg"))
      );
      $this->assertProjection(
        'space(4)',
        'space(4)',
        create(new Criteria())->setProjection(SQLFunctions::space(4))
      );
      $this->assertProjection(
        'soundex("kawabanga")',
        'soundex("kawabanga")',
        create(new Criteria())->setProjection(SQLFunctions::soundex("kawabanga"))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function concatStringTest() {
      $this->assertProjection(
        'concat("aa", cast(sysdate() as char), "cc") as "concatTest"',
        '("aa" + convert(varchar, getdate()) + "cc") as "concatTest"',
        create(new Criteria())->setProjection(SQLFunctions::concat('aa', SQLFunctions::str(SQLFunctions::getdate()), 'cc'), 'concatTest')
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function dateFunctionTest() {
      $date= new Date();
      $myDate= $date->toString($this->myconn->getFormatter()->dialect->dateFormat);
      $syDate= $date->toString($this->syconn->getFormatter()->dialect->dateFormat);
      $this->assertProjection(
        'cast(sysdate() as char)',
        'convert(varchar, getdate())',
        create(new Criteria())->setProjection(SQLFunctions::str(SQLFunctions::getdate()))
      );
      $this->assertProjection(
        'cast(timestampadd(month, -4, sysdate()) as char)',
        'convert(varchar, dateadd(month, -4, getdate()))',
        create(new Criteria())->setProjection(SQLFunctions::str(SQLFunctions::dateadd('month', '-4', SQLFunctions::getdate())))
      );
      $this->assertProjection(
        'timestampdiff(second, timestampadd(day, -4, sysdate()), sysdate())',
        'datediff(second, dateadd(day, -4, getdate()), getdate())',
        create(new Criteria())->setProjection(SQLFunctions::datediff('second', SQLFunctions::dateadd('day', '-4', SQLFunctions::getdate()), SQLFunctions::getdate()))
      );
      $this->assertProjection(
        'cast(extract(hour from sysdate()) as char)',
        'datename(hour, getdate())',
        create(new Criteria())->setProjection(SQLFunctions::datename('hour', SQLFunctions::getdate()))
      );
      $this->assertProjection(
        'extract(hour from "'.$myDate.'")',
        'datepart(hour, "'.$syDate.'")',
        create(new Criteria())->setProjection(SQLFunctions::datepart('hour', $date))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function mathArithFunctionTest() {
      $this->assertProjection(
        'abs(-6)',
        'abs(-6)',
        create(new Criteria())->setProjection(SQLFunctions::abs(-6))
      );
      $this->assertProjection(
        'ceil(5.1)',
        'ceiling(5.1)',
        create(new Criteria())->setProjection(SQLFunctions::ceil(5.1))
      );
      $this->assertProjection(
        'floor(5.7)',
        'floor(5.7)',
        create(new Criteria())->setProjection(SQLFunctions::floor(5.7))
      );
      $this->assertProjection(
        'exp(log(1))',
        'exp(log(1))',
        create(new Criteria())->setProjection(SQLFunctions::exp(SQLFunctions::log(1)))
      );
      $this->assertProjection(
        'log10(power(10, 5))',
        'log10(power(10, 5))',
        create(new Criteria())->setProjection(SQLFunctions::log10(SQLFunctions::power(10, 5)))
      );
      $this->assertProjection(
        'power(10, log10(5))',
        'power(10, log10(5))',
        create(new Criteria())->setProjection(SQLFunctions::power(10, SQLFunctions::log10(5)))
      );
      $this->assertProjection(
        'round(1.5, 0) as "roundtest1", round(1.49, 0) as "roundtest2", round(1.49, 1) as "roundtest3"',
        'round(1.5, 0) as "roundtest1", round(1.49, 0) as "roundtest2", round(1.49, 1) as "roundtest3"',
        create(new Criteria())->setProjection(Projections::ProjectionList()
          ->add(SQLFunctions::round(1.50),    'roundtest1')
          ->add(SQLFunctions::round(1.49),    'roundtest2')
          ->add(SQLFunctions::round(1.49, 1), 'roundtest3')
        )
      );
      $this->assertProjection(
        'sign(-7) as "signTest1", sign(0) as "signTest2", sign(4) as "signTest3"',
        'convert(int, sign(-7)) as "signTest1", convert(int, sign(0)) as "signTest2", convert(int, sign(4)) as "signTest3"',
        create(new Criteria())->setProjection(Projections::ProjectionList()
          ->add(SQLFunctions::sign(-7), 'signTest1')
          ->add(SQLFunctions::sign(0),  'signTest2')
          ->add(SQLFunctions::sign(4),  'signTest3')
        )
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function mathTrigFunctionTest() {
      $this->assertProjection(
        'cot(45)',
        'cot(45)',
        create(new Criteria())->setProjection(SQLFunctions::cot(45))
      );
      $this->assertProjection(
        'pi()',
        'pi()',
        create(new Criteria())->setProjection(SQLFunctions::pi())
      );
      $this->assertProjection(
        'acos(cos(0.125))',
        'acos(cos(0.125))',
        create(new Criteria())->setProjection(SQLFunctions::acos(SQLFunctions::cos(0.125)))
      );
      $this->assertProjection(
        'asin(sin(0.125))',
        'asin(sin(0.125))',
        create(new Criteria())->setProjection(SQLFunctions::asin(SQLFunctions::sin(0.125)))
      );
      $this->assertProjection(
        'atan(tan(0.125))',
        'atan(tan(0.125))',
        create(new Criteria())->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125)))
      );
      $this->assertProjection(
        'atan2(tan(0.125), 0)',
        'atn2(tan(0.125), 0)',
        create(new Criteria())->setProjection(SQLFunctions::atan(SQLFunctions::tan(0.125), 0))
      );
      $this->assertProjection(
        'degrees(pi())',
        'convert(float, degrees(pi()))',
        create(new Criteria())->setProjection(SQLFunctions::degrees(SQLFunctions::pi()))
      );
      $this->assertProjection(
        'radians(degrees(90))',
        'convert(float, radians(convert(float, degrees(90))))',
        create(new Criteria())->setProjection(SQLFunctions::radians(SQLFunctions::degrees(90)))
      );
      $this->assertProjection(
        'radians(degrees(90))',
        'convert(float, radians(convert(float, degrees(90))))',
        create(new Criteria())->setProjection(SQLFunctions::radians(SQLFunctions::degrees(90)))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function randFunctionTest() {
      $this->assertProjection(
        'rand()',
        'rand()',
        create(new Criteria())->setProjection(SQLFunctions::rand())
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function castFunctionTest() {
      $this->assertProjection(
        'cast("345" as decimal)',
        'convert("345", decimal)',
        create(new Criteria())->setProjection(SQLFunctions::cast("345", 'decimal'))
      );
      $this->assertProjection(
        'cast(job_id as char)',
        'convert(job_id, char)',
        create(new Criteria())->setProjection(SQLFunctions::cast(Job::column("job_id"), 'char'))
      );
    }
      
    /**
     * test the function set
     *
     */
    #[@test]
    function restrictionTest() {
      $this->assertSQL(
        'where job_id = ceil(asin(sin(0.125)))',
        'where job_id = ceiling(asin(sin(0.125)))',
        create(new Criteria())->add(Restrictions::equal("job_id", SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))))
      );
      $this->assertSQL(
        'where job_id = ceil(asin(sin(0.125)))',
        'where job_id = ceiling(asin(sin(0.125)))',
        create(new Criteria())->add(Restrictions::equal(Job::column("job_id"), SQLFunctions::ceil(SQLFunctions::asin(SQLFunctions::sin(0.125)))))
      );
    }
  }
?>
