<?php
/* This class is part of the XP framework
 *
 * $Id: DBXmlGenerator.class.php 9500 2007-02-26 15:40:21Z friebe $
 */

  uses(
    'lang.System',
    'rdbms.DBTable',
    'rdbms.util.DBXMLNamingContext',
    'xml.Tree'
  );
  
  /**
   * Generate an XML representation of a database table
   *
   * @see   xp://rdbms.DBTable
   */
  class DBXmlGenerator extends Object {
    public
      $doc= NULL;
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->doc= new Tree();
    }

    /**
     * Create XML from a DBTable
     *
     * @param   rdbms.DBTable table
     * @param   string dbhost
     * @param   string database
     * @return  rdbms.util.DBXmlGenerator object
     */    
    public static function createFromTable(DBTable $table, $dbhost, $database) {
      $g= new self();
      $g->doc->root->setAttribute('created_at', date('r'));
      $g->doc->root->setAttribute('created_by', System::getProperty('user.name'));
      
      $t= $g->doc->root->addChild(new Node('table', NULL, array(
        'name'     => $table->name,
        'dbhost'   => $dbhost,
        'database' => $database
      )));
      
      // Attributes
      if ($attr= $table->getFirstAttribute()) do {
        $t->addChild(new Node('attribute', NULL, array(
          'name'     => trim($attr->getName()),
          'type'     => $attr->getTypeString(),
          'identity' => $attr->isIdentity()  ? 'true' : 'false',
          'typename' => $attr->typeName(),
          'nullable' => $attr->isNullable() ? 'true' : 'false',
        )));
      } while ($attr= $table->getNextAttribute());

      // Attributes
      if ($index= $table->getFirstIndex()) do {
        $n= $t->addChild(new Node('index', NULL, array(
          'name'    => trim($index->getName()),
          'unique'  => $index->isUnique() ? 'true' : 'false',
          'primary' => $index->isPrimaryKey() ? 'true' : 'false',
        )));
        foreach ($index->getKeys() as $key) {
          $n->addChild(new Node('key', $key));
        }
      } while ($index= $table->getNextIndex());
      
      // constraints
      if ($constraint= $table->getFirstForeignKeyConstraint()) do {
        $cn= $t->addChild(new Node('constraint', NULL, array(
          'name' => trim($constraint->getName()),
        )));
        $fgn= $cn->addChild(new Node('reference', NULL, array(
          'table' => $constraint->getSource(),
          'role'  => DBXMLNamingContext::foreignKeyConstraintName($table, $constraint),
        )));
        foreach ($constraint->getKeys() as $attribute => $sourceattribute) {
          $fgn->addChild(new Node('key', NULL, array(
            'attribute'       => $attribute,
            'sourceattribute' => $sourceattribute
          )));
        }
        
      } while ($constraint= $table->getNextForeignKeyConstraint());
      
      return $g;
    }

    /**
     * Get XML source
     *
     * @return  string xml representation
     */    
    public function getSource() {
      return $this->doc->getSource(FALSE);
    }
  }
?>
