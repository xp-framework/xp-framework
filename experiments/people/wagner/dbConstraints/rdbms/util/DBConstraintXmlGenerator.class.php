<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.System',
    'rdbms.DBTable',
    'rdbms.util.DBXMLNamingContext',
    'xml.Tree'
  );
  
  /**
   * Generate the relation map of a database
   *
   * @see   xp://rdbms.DBTable
   */
  class DBConstraintXmlGenerator extends Object {
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
      
      $d= $g->doc->root->addChild(new Node('database', NULL, array(
        'database' => $database
      )));
      
      foreach (DBTable::getByDatabase($adapter, $database) as $t) {
        $tn= $d->addChild(new Node('table', NULL, array(
          'name' => $t->name,
        )));

        if ($constraint= $t->getFirstForeignKeyConstraint()) do {
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
