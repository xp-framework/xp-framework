<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'rdbms.DBTable',
    'xml.Tree',
    'lang.System',
    'util.log.Traceable',
    'rdbms.util.DBXMLNamingContext'
  );
  
  /**
   * Generate an XML representation of a database table
   *
   * @see   xp://rdbms.DBTable
   */
  class DBXmlGenerator extends Object implements Traceable {
    protected
      $cat= NULL;

    public
      $doc   = NULL,
      $table = NULL;
      
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
      
      $g->doc->root->addChild(new Node('table', NULL, array(
        'name'     => $table->name,
        'dbhost'   => $dbhost,
        'database' => $database
      )));
      $g->table= $table;
      return $g;
    }
    
    /**
     * Get XML source
     *
     * @return  string source
     */
    public function getSource() {
      $indexes= array();

      // Attributes
      with ($t= $this->doc->root->children[0]); {
        $t->children= array();
        if ($attr= $this->table->getFirstAttribute()) do {
          $t->addChild(new Node('attribute', NULL, array(
            'name'     => trim($attr->getName()),
            'type'     => $attr->getTypeString(),
            'identity' => $attr->isIdentity()  ? 'true' : 'false',
            'typename' => $attr->typeName(),
            'nullable' => $attr->isNullable() ? 'true' : 'false',
            'length'   => $attr->getLength(),
          )));
        } while ($attr= $this->table->getNextAttribute());

        // Indexes
        if ($index= $this->table->getFirstIndex()) do {
          $n= $t->addChild(new Node('index', NULL, array(
            'name'    => trim($index->getName()),
            'unique'  => $index->isUnique() ? 'true' : 'false',
            'primary' => $index->isPrimaryKey() ? 'true' : 'false',
          )));

          foreach ($index->getKeys() as $key) {
            $n->addChild(new Node('key', $key));
          }
          if (isset($indexes[implode('|', $index->getKeys())]) && $this->cat) $this->cat->warn('('.implode('|', $index->getKeys()).')', 'has been indexed twice');
          $indexes[implode('|', $index->getKeys())]= TRUE;

        } while ($index= $this->table->getNextIndex());

        // constraints
        $constKeyList= array();
        if ($constraint= $this->table->getFirstForeignKeyConstraint()) do {
          if ($constKeyList[$this->constraintKey($constraint)]) {
            $this->cat && $this->cat->warn($this->table->name, 'has a double constraint'."\n".xp::stringOf($constraint));
            continue;
          }
          $constKeyList[$this->constraintKey($constraint)]= true;
          $cn= $t->addChild(new Node('constraint', NULL, array(
            'name' => trim($constraint->getName()),
          )));
          $fgn= $cn->addChild(new Node('reference', NULL, array(
            'table' => $constraint->getSource(),
            'role'  => DBXMLNamingContext::foreignKeyConstraintName($this->table, $constraint),
          )));
          foreach ($constraint->getKeys() as $attribute => $sourceattribute) {
            $fgn->addChild(new Node('key', NULL, array(
              'attribute'       => $attribute,
              'sourceattribute' => $sourceattribute
            )));
          }

        } while ($constraint= $this->table->getNextForeignKeyConstraint());
      }
      
      return $this->doc->getSource(FALSE);
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * descriptive key for constraint
     *
     * @param   rdbms.DBForeignKeyConstraint
     * @return  string
     */
    private function constraintKey($c) {
      return $c->source.'#'.implode('|', array_keys($c->keys)).'#'.implode('|', array_values($c->keys));
    }
  }
?>
