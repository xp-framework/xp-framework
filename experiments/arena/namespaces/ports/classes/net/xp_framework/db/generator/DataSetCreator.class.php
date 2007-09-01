<?php
/* This class is part of the XP framework
 *
 * $Id: DataSetCreator.class.php 10863 2007-07-25 09:57:03Z ruben $ 
 */

  namespace net::xp_framework::db::generator;

  ::uses(
    'io.File',
    'io.FileUtil',
    'io.Folder',
    'rdbms.DSN',
    'rdbms.DBTable',
    'rdbms.DriverManager',
    'rdbms.util.DBConstraintXmlGenerator', 
    'rdbms.util.DBXMLNamingContext',
    'rdbms.util.DBXmlGenerator', 
    'util.log.Logger',
    'util.log.FileAppender',
    'util.Properties',
    'util.cmd.Command',
    'util.cmd.ParamString',
    'util.log.ColoredConsoleAppender',
    'xml.DomXSLProcessor'
  );

  /**
   * DB-XML-file and DB-PHP class generator
   *
   * Supports the following database drivers:
   * <ul>
   *   <li>mysql</li>
   *   <li>sybase</li>
   *   <li>sqlite</li>
   *   <li>pgsql</li>
   * </ul>
   *
   * @test     xp://net.xp_framework.unittest.cmd.DataSetCreatorTest
   * @purpose  Infrastructure
   */
  class DataSetCreator extends util::cmd::Command {
    public static $adapters= array(
      'mysql'   => 'rdbms.mysql.MySQLDBAdapter',
      'sqlite'  => 'rdbms.sqlite.SQLiteDBAdapter',
      'pgsql'   => 'rdbms.pgsql.PostgreSQLDBAdapter',
      'sybase'  => 'rdbms.sybase.SybaseDBAdapter',
    );
    
    const GENERATE_XML= 'generateTables';
    const GENERATE_SRC= 'xsltproc';
    
    protected 
      $mode= self::GENERATE_XML,
      $xmltarget,
      $reltarget,
      $dsntemp,
      $prefix,
      $prefixRemove,
      $incprefix,
      $exprefix,
      $connection,
      $package,
      $xmlfile,
      $outputdir,
      $naming= NULL,
      $ignore;
    
    /**
     * Get prefixed classname
     *
     * @param   string tname table name
     * @param   string prefix default ''
     * @param   string[] include default array()
     * @param   string[] exclude default array()
     * @return  string
     * @throws  lang.IllegalArgumentException
     */
    public function prefixedClassName($tname, $prefix= '', $include= array(), $exclude= array(), $remove= '') {
      $p= ''; $r= '';
      if (!empty($exclude) && !empty($include)) {
        throw new lang::IllegalArgumentException('Unknown use-case');
      } else if (!empty($exclude) && empty($include) && !in_array($tname, $exclude)) {
        $r= $remove;
        $p= $prefix;
      } else if (empty($exclude) && !empty($include) && in_array($tname, $include)) {
        $r= $remove;
        $p= $prefix;
      }

      // Perform removal, if wanted
      if (strlen($r) && 0 === strpos($tname, $r)) $tname= substr($tname, strlen($r));
      return $p.ucfirst(strtolower($tname));
    }

    /**
     * Sets up connection to the database
     *
     * @param   string dsntemp
     * @return  rdbms.DBAdapter
     * @throws  lang.IllegalArgumentException if the driver is not supported
     */
    public function getAdapter($dsntemp) {
      $dsn= new rdbms::DSN($dsntemp);
      if (!isset(self::$adapters[$dsn->getDriver()])) {
        throw new lang::IllegalArgumentException('Unsupported driver "'.$dsn->getDriver().'"');
      }

      // Check whether host is connection or connection is set in .ini
      if (empty($this->connection)) {
        $this->connection= $dsn->getHost();
      }

      // Get connection
      return lang::XPClass::forName(self::$adapters[$dsn->getDriver()])->newInstance(
        rdbms::DriverManager::getInstance()->getConnection($dsntemp)
      );
    }
    
    /**
     * generates .xml documents from tables 
     *
     */
    public function generateTables() {
      $adapter= $this->getAdapter($this->dsntemp);
      $adapter->conn->connect();

      if (!empty($this->naming)) rdbms::util::DBXMLNamingContext::setStrategy(lang::XPClass::forName($this->naming)->newInstance());

      // Create new Folder Object and new Folder(s) if necessary
      $fold= new io::Folder($this->xmltarget);
      $relfold= new io::Folder($this->reltarget);
      $fold->exists() || $fold->create(0755);
      $relfold->exists() || $relfold->create(0755);

      $tables= rdbms::DBTable::getByDatabase($adapter, $adapter->conn->dsn->getDatabase());
      foreach ($tables as $t) {
        if (!in_array(strtolower($t->name), $this->ignore)) {

          // Generate XML
          $gen= rdbms::util::DBXmlGenerator::createFromTable(
            $t, 
            $this->connection,          
            $adapter->conn->dsn->getDatabase()
          ); 
          $gen->setTrace(util::log::Logger::getInstance()->getCategory());

          // Determine whether filename needs prefix
          $classname= $this->prefixedClassName(
            $t->name, 
            $this->prefix, 
            $this->incprefix, 
            $this->exprefix,
            $this->prefixRemove
          );
          $filename= ucfirst($t->name);
          // Create table node...
          with ($node= $gen->doc->root->children[0]); {
            $node->setAttribute('dbtype', $adapter->conn->dsn->getDriver());
            $node->setAttribute('class', $classname);
            $node->setAttribute('package', $this->package);
          }

          // ...and finally, write to a file
          $f= new io::File($fold->getURI().ucfirst($t->name).'.xml');
          $written= io::FileUtil::setContents($f, $gen->getSource());
          $this->out->writeLinef(
            '---> Output written to %s (%.2f kB)', 
            $f->getURI(),
            $written / 1024
          );
        }
      }
      
      $cg= rdbms::util::DBConstraintXmlGenerator::createFromDatabase($adapter, $adapter->conn->dsn->getDatabase());
      $cg->setTrace(util::log::Logger::getInstance()->getCategory());
      
      $f= new io::File($relfold->getURI().'constraints.xml');
      $written= io::FileUtil::setContents($f, $cg->getSource());
      $this->out->writeLinef(
        '===> Output written to %s (%.2f kB)', 
        $f->getURI(),
        $written / 1024
      );
    }
    
    /**
     * Uses xsltProc to convert xml files to php code
     *
     */
    public function xsltproc() {
      $directory= str_replace('.', DIRECTORY_SEPARATOR, $this->package);    

      preg_match('/[0-9a-z_-]+\.xml/i', $this->xmlfile, $matches);
      $name= strtolower(str_replace('.xml', '', $matches[0]));
      $proc= new xml::DomXSLProcessor();
      
      // Using override XSL-File
      if (is_array($this->overrides) && array_key_exists($name, $this->overrides)) {
        $proc->setXSLFile(str_replace('config.ini', $this->overrides[$name], $this->inifile));
        $this->out->writeLinef('!!! Using override xslfile: %s', $this->overrides[$name]);
      } else {
        $proc->setXSLBuf($this->xsl);
      }
      
      $proc->setXMLFile($this->xmlfile);
      $proc->setParam('definitionpath', realpath(dirname($this->xmlfile)));
      $proc->setParam('constraintfile', realpath(dirname(dirname($this->xmlfile))).'/constraints/constraints.xml');
      $proc->setParam('package',        $this->package);
      $proc->setParam('prefix',         $this->prefix);
      $proc->setParam('incprefix',      implode(',', $this->incprefix));
      $proc->setParam('exprefix',       implode(',', $this->exprefix));
      $proc->setParam('prefixRemove',   $this->prefixRemove);
      $proc->run();

      // Dump any errors (warnings e.g.)
      if ($e= ::xp::registry('errors')) {
        $this->out->writeLine(::xp::stringOf($e));
      }

      $fold= new io::Folder($this->outputdir.DIRECTORY_SEPARATOR.$directory);
      $fold->exists() || $fold->create(0755);
      
      $filename= $this->prefixedClassName($name, $this->prefix, $this->incprefix, $this->exprefix, $this->prefixRemove);
      
      $f= new io::File($fold->getURI().DIRECTORY_SEPARATOR.$filename.'.class.php');
      $written= io::FileUtil::setContents($f, $proc->output());    
      $this->out->writeLinef('---> Writing to %s (%.2f kB)', $f->getURI(), $written / 1024);
    }
    
    /**
     * Set config.ini filename
     *
     * @param   string filename default 'config.ini'
     */
    #[@arg]
    public function setConfig($filename) {
      $ini= new util::Properties($filename);
      if (!$ini->exists()) {
        throw new io::FileNotFoundException('No config file found. Use --help for more details');
      }

      $this->xmltarget    = str_replace('config.ini', 'tables', $ini->getFilename());
      $this->reltarget    = str_replace('config.ini', 'constraints', $ini->getFilename());
      $this->dsntemp      = $ini->readString('connection', 'dsn');
      $this->prefix       = $ini->readString('prefix', 'value');
      $this->prefixRemove = $ini->readString('prefix', 'remove');
      $this->incprefix    = $ini->readArray ('prefix', 'include');
      $this->exprefix     = $ini->readArray ('prefix', 'exclude');
      $this->connection   = $ini->readString('connection', 'name');
      $this->package      = $ini->readString('mapping', 'package');
      $this->naming       = $ini->readString('mapping', 'naming');
      $this->overrides    = $ini->readSection('overrides', FALSE);
      $this->inifile      = $ini->getFilename();
      $this->ignore       = $ini->readArray('ignore', 'tables');
      if (!empty($this->incprefix) && !empty($this->exprefix)) {
        throw new lang::IllegalArgumentException(
          '==> exclude-prefix AND include-prefix are set. This is invalid <=='."\n".
          '==> and will probably cause a rift in the space/time continuum!<=='
        );
      }
    }
    
    /**
     * Supply whether to generate sourcecode from the XML files
     *
     * @param   string xml default NULL
     */
    #[@arg(name= 'xmlgen', short= 'X')]
    public function doGenerateSource($xml= NULL) {
      if (empty($xml)) {
        $this->mode= self::GENERATE_XML;
      } else {
        $this->mode= self::GENERATE_SRC;
        $this->xmlfile= $xml;
      }
    }
    
    /**
     * Supply stylesheet to use for sourcecode generation
     *
     * @param   string xsl default NULL the stylesheet to use
     */
    #[@arg(name= 'xslsheet', short= 'S')]
    public function setStylesheet($xsl= NULL) {
      if (self::GENERATE_SRC != $this->mode) return;
      
      // Set XSL file (default to xp5.php.xsl if not specified)
      $this->xsl= empty($xsl)
        ? $this->getClass()->getPackage()->getResource('xp5.php.xsl')
        : io::FileUtil::getContents(new io::File($xsl));
    }

    /**
     * Sets the output directory for the php classes.
     *
     * @param   string out default . the directory to use
     */
    #[@arg(name= 'output', short= 'O')]
    public function setOutputdir($out= ".") {
      $this->outputdir=rtrim($out, '/');
    }
    
    /**
     * Sets whether to be verbose
     *
     */
    #[@arg]
    public function setVerbose() {
      util::log::Logger::getInstance()->getCategory()->addAppender(new util::log::ColoredConsoleAppender());
    }

    /**
     * Run this command
     *
     */
    public function run() {
      $this->getClass()->getMethod($this->mode)->invoke($this);
    }   
  }
?>
