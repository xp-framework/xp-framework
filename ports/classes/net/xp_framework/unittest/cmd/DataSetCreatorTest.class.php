 <?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.db.generator.DataSetCreator'
  );

  /**
   * TestCase
   *
   * @see      xp://net.xp_framework.db.generator.DataSetCreator
   * @purpose  Unit test
   */
  class DataSetCreatorTest extends TestCase {
    protected
      $fixture= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new DataSetCreator();
    }
    
    /**
     * Test situation without prefixes
     *
     */
    #[@test]
    public function noPrefix() {
      $this->assertEquals('Author', $this->fixture->prefixedClassName('author'));
    }

    /**
     * Test situation with a prefix but without in- and excludes
     *
     */
    #[@test]
    public function cmsPrefix() {
      $this->assertEquals('Author', $this->fixture->prefixedClassName('author', 'Cms'));
    }

    /**
     * Test situation with a prefix and includes
     *
     */
    #[@test]
    public function prefixAndTableInIncludes() {
      $this->assertEquals('CmsAuthor', $this->fixture->prefixedClassName('author', 'Cms', array('author')));
    }
    
    /**
     * Test situation with a prefix and includes
     *
     */
    #[@test]
    public function prefixAndTableNotInIncludes() {
      $this->assertEquals('Category', $this->fixture->prefixedClassName('category', 'Cms', array('author')));
    }

    /**
     * Test situation with a prefix and excludes
     *
     */
    #[@test]
    public function prefixAndTableInExcludes() {
      $this->assertEquals('Author', $this->fixture->prefixedClassName('author', 'Cms', array(), array('author')));
    }

    /**
     * Test situation with a prefix and excludes
     *
     */
    #[@test]
    public function prefixAndTableNotInExcludes() {
      $this->assertEquals('CmsAuthor', $this->fixture->prefixedClassName('author', 'Cms', array('author'), array()));
    }

    /**
     * Test situation when both include *and* excludes are set
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function includesAndExcludesSet() {
      $this->fixture->prefixedClassName('author', 'Cms', array('author'), array('author'));
    }

    /**
     * Test situation when a correct dsn is supplied (mysql)
     *
     */
    #[@test]
    public function mysqlAdapter() {
      $adapter= $this->fixture->getAdapter('mysql://user:password@host/DATABASE');
      $this->assertClass($adapter, 'rdbms.mysql.MySQLDBAdapter');
      
      with ($dsn= $adapter->conn->dsn); {
        $this->assertEquals('mysql', $dsn->getDriver());
        $this->assertEquals('user', $dsn->getUser());
        $this->assertEquals('password', $dsn->getPassword());
        $this->assertEquals('host', $dsn->getHost());
        $this->assertEquals('DATABASE', $dsn->getDatabase());
      }
    }

    /**
     * Test situation when a correct dsn is supplied (sybase)
     *
     */
    #[@test]
    public function sybaseAdapter() {
      $adapter= $this->fixture->getAdapter('sybase://user:password@host/DATABASE');
      $this->assertClass($adapter, 'rdbms.sybase.SybaseDBAdapter');
      
      with ($dsn= $adapter->conn->dsn); {
        $this->assertEquals('sybase', $dsn->getDriver());
        $this->assertEquals('user', $dsn->getUser());
        $this->assertEquals('password', $dsn->getPassword());
        $this->assertEquals('host', $dsn->getHost());
        $this->assertEquals('DATABASE', $dsn->getDatabase());
      }
    }

    /**
     * Test situation when a wrong scheme is supplied. This test relies
     * on the fact that a "binford" database driver is not supported!
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unsupportedAdapter() {
      $this->fixture->getAdapter('binford://user:password@host/DATABASE');
    }
  }
?>
