<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.System',
    'rdbms.DBTable',
    'rdbms.util.DBXMLNamingContext',
    'util.log.Traceable',
    'xml.Tree'
  );
  
  /**
   * Generate the relation map of a database
   *
   * @see   xp://rdbms.DBTable
   */
  class DBConstraintXmlGenerator extends Object implements Traceable {
    protected
      $cat= NULL;

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
     * Create XML map
     *
     * @param   rdbms.DBAdapter and adapter
     * @param   string database
     * @return  rdbms.util.DBConstraintXmlGenerator object
     */    
    public static function createFromDatabase($adapter, $database) {
      $g= new self();
      $g->doc->root->setAttribute('created_at', date('r'));
      $g->doc->root->setAttribute('created_by', System::getProperty('user.name'));
      
      $g->doc->root->addChild(new Node('database', NULL, array(
        'database' => $database
      )));
      
      $g->tables= DBTable::getByDatabase($adapter, $database);
      return $g;
    }

    /**
     * Get XML tree
     *
     * @return  xml.Tree
     */    
    public function getTree() {
      foreach ($this->tables as $t) {
        $constKeyList= array();
        $tn= $this->doc->root->children[0]->addChild(new Node('table', NULL, array(
          'name' => $t->name,
        )));

        if ($constraint= $t->getFirstForeignKeyConstraint()) do {
          if (isset($constKeyList[$this->constraintKey($constraint)])) {
            $this->cat && $this->cat->warn($t->name, 'has a double constraint'."\n".xp::stringOf($constraint));
            continue;
          }
          $constKeyList[$this->constraintKey($constraint)]= true;
          $cn= $tn->addChild(new Node('constraint', NULL, array(
            'name' => trim($constraint->getName()),
          )));
          $fgn= $cn->addChild(new Node('reference', NULL, array(
            'table' => $constraint->getSource(),
            'role'  => DBXMLNamingContext::referencingForeignKeyConstraintName($t, $constraint),
          )));
          foreach ($constraint->getKeys() as $attribute => $sourceattribute) {
            $fgn->addChild(new Node('key', NULL, array(
              'attribute'       => $attribute,
              'sourceattribute' => $sourceattribute,
            )));
          }

        } while ($constraint= $t->getNextForeignKeyConstraint());
      }
      return $this->doc;
    }

    /**
     * Get XML source
     *
     * @return  string xml representation
     */    
    public function getSource() {
      return $this->getTree()->getSource(FALSE);
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
