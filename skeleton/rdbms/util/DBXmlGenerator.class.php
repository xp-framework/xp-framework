<?php
    uses('rdbms.DBTable', 'xml.Tree', 'lang.System');
    
    class DBXmlGenerator extends Object {
        var
            $doc= NULL;
            
        function __construct() {
            $this->doc= &new Tree();
            parent::__construct();
        }
        
        function &createFromTable(&$table) {
            if (!is_a($table, 'DBTable')) {
                return throw(new IllegalArgumentException('Argument table is not a DBTable object'));
            }
            
            $g= &new DBXmlGenerator();
            
            $prop= System::getInfo();
            $g->doc->root->attribute['created_at']= date('r');
            $g->doc->root->attribute['created_by']= $prop['user.name'];
            
            $t= &$g->doc->root->addChild(new Node(array(
                  'name' => 'table'
            )));
            $t->attribute['name']= $table->name;
            
            // Attributes
            $attr= &$table->getFirstAttribute();
            do {
                $n= &$t->addChild(new Node(array(
                    'name'    => 'attribute'
                )));
                $n->attribute['name']= $attr->getName();
                $n->attribute['type']= $attr->getTypeString();
                $n->attribute['identity']= $attr->isIdentity()  ? 'true' : 'false';
                $n->attribute['typename']= $attr->typeName();
                $n->attribute['nullable']= $attr->isNullable() ? 'true' : 'false';
            } while ($attr= &$table->getNextAttribute());

            // Attributes
            $index= &$table->getFirstIndex();
            do {
                $n= &$t->addChild(new Node(array(
                    'name'    => 'index'
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
        
        function getSource() {
            return $this->doc->getSource(FALSE);
        }
    }
?>
