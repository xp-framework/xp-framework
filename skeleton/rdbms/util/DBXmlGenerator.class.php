<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.DBTable', 'xml.Tree', 'lang.System');
  
  /**
   * Generate an XML representation of a database table
   *
   * @see   xp://rdbms.DBTable
   */
  class DBXmlGenerator extends Object {
    var
      $doc= NULL;
      
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->doc= &new Tree();
      parent::__construct();
    }

    /**
     * Create XML from a DBTable
     *
     * @model   static
     * @access  public
     * @param   &rdbms.DBTable table
     * @param   string dbhost
     * @param   string database
     * @return  &rdbms.util.DBXmlGenerator object
     */    
    function &createFromTable(&$table, $dbhost, $database) {
      if (!is_a($table, 'DBTable')) {
        return throw(new IllegalArgumentException('Argument table is not a DBTable object'));
      }
      
      $g= &new DBXmlGenerator();
      
      $g->doc->root->attribute['created_at']= date('r');
      $g->doc->root->attribute['created_by']= System::getProperty('user.name');
      
      $t= &$g->doc->root->addChild(new Node(array(
          'name' => 'table'
      )));
      $t->attribute['name']=      $table->name;
      $t->attribute['dbhost']=    $dbhost;
      $t->attribute['database']=  $database;
      
      // Attributes
      if ($attr= &$table->getFirstAttribute()) do {
        $n= &$t->addChild(new Node(array(
          'name'  => 'attribute'
        )));
        $n->attribute['name']= $attr->getName();
        $n->attribute['type']= $attr->getTypeString();
        $n->attribute['identity']= $attr->isIdentity()  ? 'true' : 'false';
        $n->attribute['typename']= $attr->typeName();
        $n->attribute['nullable']= $attr->isNullable() ? 'true' : 'false';
      } while ($attr= &$table->getNextAttribute());

      // Attributes
      if ($index= &$table->getFirstIndex()) do {
        $n= &$t->addChild(new Node(array(
          'name'  => 'index'
        )));
        $n->attribute['name']= $index->getName();
        $n->attribute['unique']= $index->isUnique() ? 'true' : 'false';
        $n->attribute['primary']= $index->isUnique() ? 'true' : 'false';
        foreach ($index->getKeys() as $key) {
          $k= &$n->addChild(new Node(array('name' => 'key')));
          $k->setContent($key);
        }
      } while ($index= &$table->getNextIndex());
      
      return $g;
    }

    /**
     * Get XML source
     *
     * @access  public
     * @return  string xml representation
     */    
    function getSource() {
      return $this->doc->getSource(FALSE);
    }
  }
?>
