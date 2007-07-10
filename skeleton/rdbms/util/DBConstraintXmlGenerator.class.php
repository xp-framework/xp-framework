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
     * Get XML source
     *
     * @return  string xml representation
     */    
    public function getSource() {
      foreach ($this->tables as $t) {
        $rolenameList= array();
        $tn= $this->doc->root->children[0]->addChild(new Node('table', NULL, array(
          'name' => $t->name,
        )));

        if ($constraint= $t->getFirstForeignKeyConstraint()) do {
          $rolename= DBXMLNamingContext::referencingForeignKeyConstraintName($t, $constraint);
          if ($rolenameList[$rolename]) {
            $this->cat && $this->cat->warn($rolename, 'is a double role name');
          }
          $rolenameList[$rolename]= true;
          $cn= $tn->addChild(new Node('constraint', NULL, array(
            'name' => trim($constraint->getName()),
          )));
          $fgn= $cn->addChild(new Node('reference', NULL, array(
            'table' => $constraint->getSource(),
            'role'  => $rolename,
          )));
          foreach ($constraint->getKeys() as $attribute => $sourceattribute) {
            $fgn->addChild(new Node('key', NULL, array(
              'attribute'       => $attribute,
              'sourceattribute' => $sourceattribute,
            )));
          }

        } while ($constraint= $t->getNextForeignKeyConstraint());
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
  }
?>
