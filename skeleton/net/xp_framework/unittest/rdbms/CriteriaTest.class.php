<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'rdbms.Criteria', 
    'rdbms.DriverManager',
    'net.xp_framework.unittest.rdbms.dataset.Job',
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test criteria class
   *
   * @see      xp://rdbms.Criteria
   * @purpose  Unit Test
   */
  class CriteriaTest extends TestCase {
    var
      $conn = NULL,
      $peer = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      parent::__construct($name);
      $this->conn= &DriverManager::getConnection('sybase://localhost:1999/');
      $this->peer= &Job::getPeer();
    }
    
    /**
     * Test 
     *
     * @access  protected
     * @param   string sql
     * @param   &rdbms.Criteria criteria
     * @throws  util.profiling.unittest.AssertionFailedError
     */
    function assertSql($sql, &$criteria) {
      $this->assertEquals($sql, trim($criteria->toSQL($this->conn, $this->peer->types), ' '));
    }
      
    /**
     * Test that an "empty" criteria object will return an empty where 
     * statetement
     *
     * @access  public
     */
    #[@test]
    function emptyCriteria() {
      $this->assertSql('', new Criteria());
    }

    /**
     * Tests a criteria object with one equality comparison
     *
     * @access  public
     */
    #[@test]
    function simpleCriteria() {
      $this->assertSql('where job_id = 1', new Criteria(array('job_id', 1, EQUAL)));
    }

    /**
     * Tests Criteria::toSQL() will throw an exception when using a non-
     * existant field
     *
     * @access  public
     */
    #[@test, @expect('rdbms.SQLStateException')]
    function nonExistantFieldCausesException() {
      $criteria= &new Criteria(array('non-existant-field', 1, EQUAL));
      $criteria->toSQL($this->conn, $this->peer->types);
    }

    /**
     * Tests a more complex criteria object
     *
     * @access  public
     */
    #[@test]
    function complexCriteria() {
      with ($c= &new Criteria()); {
        $c->add('job_id', 1, EQUAL);
        $c->add('valid_from', new Date('2006-01-01'), GREATER_EQUAL);
        $c->add('title', 'Hello%', LIKE);
        $c->addOrderBy('valid_from');
      }

      // Note we're relying on the connection to be a sybase connection -
      // otherwise, quoting and date representation may change and make
      // this test fail.
      $this->assertSql(
        'where job_id = 1 and valid_from >= "2006-01-01 12:00AM" and title like "Hello%" order by valid_from asc', 
        $c
      );
    }
  }
?>
