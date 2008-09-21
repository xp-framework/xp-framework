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
   * Generates DataSet classes
   *
   * @purpose  Code generator
   */
  class xp·codegen·dataset·Generator extends AbstractGenerator {
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

      // Setup generator
      $this->processor= new DomXSLProcessor();
      $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource($args->value('lang', 'l', 'xp5.php').'.xsl'));
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
          $this->adapter->conn,          
          $this->adapter->conn->dsn->getDatabase()
        );
        
        // Calculate classname
        $className= ucfirst($table->name);
        
        // Add extra information
        with ($node= $gen->doc->root->children[0]); {
          $node->setAttribute('dbtype', $this->adapter->conn->dsn->getDriver());
          $node->setAttribute('class', $className);
          $node->setAttribute('package', $this->package);
          $node->addChild($constraints->root);
        }

        $xml[]= $storage->write($className, $gen->getSource());
      }
      return $xml;
    }

    /**
     * Apply XSLT stylesheet and generate sourcecode
     *
     */
    #[@target(input= array('generateTableXml', 'output'))]
    public function generateCode($tables, $output) {
      foreach ($tables as $stored) {
        $this->processor->setXMLBuf($stored->data());
        $this->processor->run();
        
        $output->append($stored->name().xp::CLASS_FILE_EXT, $this->processor->output());
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
