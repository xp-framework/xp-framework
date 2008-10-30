<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.codegen.dataset';
  
  uses(
    'xp.codegen.AbstractGenerator',
    'rdbms.DSN', 
    'rdbms.DBTable',
    'rdbms.DriverManager',
    'rdbms.util.DBConstraintXmlGenerator', 
    'rdbms.util.DBXMLNamingContext',
    'rdbms.util.DBXmlGenerator',
    'xml.DomXSLProcessor'
  );

  /**
   * DataSet
   * =======
   * Generates rdbms.DataSet classes for use in the XP framework's O/R mapper.
   *
   * Usage:
   * <pre>
   *   $ cgen ... dataset {dsn} [-p {package}] [-h {host}] [-l {language}]
   * </pre>
   *
   * Options
   * -------
   * <ul>
   *   <li>package: The package name, default "db"</li>
   *   <li>host: Which connection name to use, defaults to host name from DSN</li>
   *   <li>language: Language to generate, defaults to "xp5"</li>
   * </ul>
   *
   * Languages
   * ---------
   * The following languages are supported: xp5, xp4
   *
   * @purpose  Code generator
   */
  class xp·codegen·dataset·Generator extends AbstractGenerator {
    const
      CONSTRAINT_FILE_NAME= '__Constraints';

    protected static 
      $adapters = array();
    
    protected
      $adapter  = NULL,
      $processor= NULL,
      $package  = '';
    
    static function __static() {
      self::$adapters['mysql']= XPClass::forName('rdbms.mysql.MySQLDBAdapter');
      self::$adapters['sqlite']= XPClass::forName('rdbms.sqlite.SQLiteDBAdapter');
      self::$adapters['pgsql']= XPClass::forName('rdbms.pgsql.PostgreSQLDBAdapter');
      self::$adapters['sybase']= XPClass::forName('rdbms.sybase.SybaseDBAdapter');
    }
    
    /**
     * Constructor
     *
     * @param   util.cmd.ParamString args
     */
    public function __construct(ParamString $args) {
      $dsn= new DSN($args->value(0));
      $this->adapter= self::$adapters[$dsn->getDriver()]->newInstance(
        DriverManager::getInstance()->getConnection($dsn->dsn)
      );

      $this->package= $args->value('package', 'p', 'db');
      $this->host= $args->value('host', 'h', $dsn->getHost());

      // Setup generator
      $this->processor= new DomXSLProcessor();
      $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource($args->value('lang', 'l', 'xp5.php').'.xsl'));
      $this->processor->setParam('package', $this->package);
    }
    
    /**
     * Connect the database
     *
     */
    #[@target]
    public function connect() {
      $this->adapter->conn->connect();
    }

    /**
     * Fetch tables
     *
     */
    #[@target(depends= 'connect')]
    public function fetchTables() {
      return DBTable::getByDatabase(
        $this->adapter, 
        $this->adapter->conn->dsn->getDatabase()
      );
    }

    /**
     * Fetch constraints
     *
     */
    #[@target(depends= 'connect')]
    public function fetchConstraints() {
      return DBConstraintXmlGenerator::createFromDatabase(
        $this->adapter, 
        $this->adapter->conn->dsn->getDatabase()
      )->getTree();
    }

    /**
     * Generate XML from the tables
     *
     */
    #[@target(input= array('fetchTables', 'fetchConstraints', 'storage'))]
    public function generateTableXml($tables, $constraints, $storage) {
      $xml= array();
      foreach ($tables as $table) {
        $gen= DBXmlGenerator::createFromTable(
          $table, 
          $this->host,          
          $this->adapter->conn->dsn->getDatabase()
        )->getTree();
        
        // Calculate classname
        $className= ucfirst($table->name);
        
        // Add extra information
        with ($node= $gen->root->children[0]); {
          $node->setAttribute('dbtype', $this->adapter->conn->dsn->getDriver());
          $node->setAttribute('class', $className);
          $node->setAttribute('package', $this->package);
        }
        
        $xml[]= $storage->write($className, $gen->getSource(INDENT_DEFAULT));
      }
      $storage->write(self::CONSTRAINT_FILE_NAME, $constraints->getSource(INDENT_DEFAULT));
      return $xml;
    }

    /**
     * Apply XSLT stylesheet and generate sourcecode
     *
     */
    #[@target(input= array('generateTableXml', 'output'))]
    public function generateCode($tables, $output) {
      $dir= strtr($this->package, '.', '/').'/';

      $this->processor->setParam('definitionpath', $this->storage->getUri());
      $this->processor->setParam('constraintfile', $this->storage->getUri().self::CONSTRAINT_FILE_NAME);
      foreach ($tables as $stored) {
        $this->processor->setXMLBuf($stored->data());
        $this->processor->run();
        
        $output->append($dir.$stored->name().xp::CLASS_FILE_EXT, $this->processor->output());
      }
    }

    /**
     * Creates a string representation of this generator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->adapter->conn->dsn->toString().']';
    }
  }
?>
