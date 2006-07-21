<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.Criteria', 
    'rdbms.criterion.Restrictions', 
    'rdbms.criterion.Property', 
    'rdbms.DriverManager',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test criteria class
   *
   * Note we're relying on the connection to be a sybase connection -
   * otherwise, quoting and date representation may change and make
   * this testcase fail.
   *
   * @see      xp://rdbms.Criteria
   * @purpose  Unit Test
   */
  class CriteriaTest extends TestCase {
    public
      $conn = NULL,
      $peer = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    public function __construct($name) {
      parent::__construct($name);
      $this->conn= &DriverManager::getConnection('sybase://localhost:1999/');
      $this->peer= &Job::getPeer();
    }
    
    /**
     * Helper method that will call toSQL() on the passed criteria and
     * compare the resulting string to the expected string.
     *
     * @access  protected
     * @param   string sql
     * @param   &rdbms.Criteria criteria
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    public function assertSql($sql, &$criteria) {
      $this->assertEquals($sql, trim($criteria->toSQL($this->conn, $this->peer->types), ' '));
    }
      
    /**
     * Test that an "empty" criteria object will return an empty where 
     * statetement
     *
     * @access  public
     */
    #[@test]
    public function emptyCriteria() {
      $this->assertSql('', new Criteria());
    }

    /**
     * Tests a criteria object with one equality comparison
     *
     * @access  public
     */
    #[@test]
    public function simpleCriteria() {
      $this->assertSql('where job_id = 1', new Criteria(array('job_id', 1, EQUAL)));
    }

    /**
     * Tests Criteria::toSQL() will throw an exception when using a non-
     * existant field
     *
     * @access  public
     */
    #[@test, @expect('rdbms.SQLStateException')]
    public function nonExistantFieldCausesException() {
      $criteria= &new Criteria(array('non-existant-field', 1, EQUAL));
      $criteria->toSQL($this->conn, $this->peer->types);
    }

    /**
     * Tests a more complex criteria object
     *
     * @access  public
     */
    #[@test]
    public function complexCriteria() {
      with ($c= &new Criteria()); {
        $c->add('job_id', 1, EQUAL);
        $c->add('valid_from', new Date('2006-01-01'), GREATER_EQUAL);
        $c->add('title', 'Hello%', LIKE);
        $c->addOrderBy('valid_from');
      }

      $this->assertSql(
        'where job_id = 1 and valid_from >= "2006-01-01 12:00AM" and title like "Hello%" order by valid_from asc', 
        $c
      );
    }
    
    /**
     * Tests the rdbms.criterion API
     *
     * @see     xp://rdbms.criterion.Property
     * @see     xp://rdbms.criterion.Restrictions
     * @access  public
     */
    #[@test]
    public function restrictionsFactory() {
      $job_id= &Property::forName('job_id');
      $c= &new Criteria(Restrictions::anyOf(
        Restrictions::not($job_id->in(array(1, 2, 3))),
        Restrictions::allOf(
          Restrictions::like('title', 'Hello%'),
          Restrictions::greaterThan('valid_from', new Date('2006-01-01'))
        )
      ));

      $this->assertSql(
        'where (not (job_id in (1, 2, 3)) or (title like "Hello%" and valid_from > "2006-01-01 12:00AM"))',
        $c
      );
    }
    
    /**
     * Tests Criteria constructor for varargs support
     *
     * @access  public
     */
    #[@test]
    public function constructorAcceptsVarArgArrays() {
      $this->assertSql(
        'where job_id = 1 and title = "Hello"', 
        new Criteria(array('job_id', 1, EQUAL), array('title', 'Hello', EQUAL))
      );
    }
  }
?>
