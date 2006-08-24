<?php

  uses(
    'xml.Tree',
    'org.dia.DiaCompound',
    'org.dia.DiaData',
    'org.dia.DiaLayer',
    'org.dia.DiaObject',
    'org.dia.DiaUMLClass'
  );
  
  /**
   * TODO: use(doclet); (better than reflection api)
   * Represents a 'dia' diagram
   */
  class DiaDiagram extends DiaCompound {

    var
      $ns= array('dia' => 'http://www.gnome.org/projects/dia/'),
      $node_name= 'dia:diagram';

    /************************ Static Methods ****************************/

    /**
     * Returns the next ID for an element (auto increment)
     *
     * @model static
     */
    function getId() {
      static $id= 0;
      return sprintf('%02d', $id++);
    }

    /**
     * TODO: Generate class (structure - no code) from a DIAgram 
     *
     */
    function &buildFromDia($file) {
    }

    function &createDiaFromClasses2($classnames, $recurse= 0) {
      // initialize DIAgram
      $dia= &new DiaDiagram();
      $dia->initialize();

      $layers= &$dia->getChildByType('DiaLayer');
      $layer= &$layers[0];

      // check params
      if (!is_array($classnames)) return $dia;

      // loop over classnames
      foreach ($classnames as $classname) {
        
      }
      
      
    }
    
    /**
     * Generate a DIAgram from classname(s)
     *
     * @access  public
     * @model   static
     * @param   array $classnames
     */
    function &createDiaFromClasses($classnames, $recurse= 0) {
      $dia= &new DiaDiagram();
      $dia->initialize();
      $layers= &$dia->getChildByType('DiaLayer');
      $layer= &$layers[0];

      if (!is_array($classnames)) return $dia;

      // loop over classnames
      foreach ($classnames as $classname) {
        $class= &XPClass::forName($classname);
        $uml_class= &new DiaUMLClass('UML - Class', 0);
        $dia_class= $uml_class->getClass();

        // loop over DiaUMLClass methods (annotations)
        $dia_methods= &$dia_class->getMethods();
        foreach (array_keys($dia_methods) as $key) {
          $dia_method= $dia_methods[$key];
          if (!$dia_method->hasAnnotation('fromClass')) continue;
          if (!$dia_method->hasAnnotation('fromClass', 'type')) continue;

          $annotation= $dia_method->getAnnotation('fromClass', 'type');
          switch ($annotation) {
            case 'string':
            case 'bool':
              if ($dia_method->hasAnnotation('fromClass', 'eval')) {
                $ann= $dia_method->getAnnotation('fromClass', 'eval');
                $value= eval("return $ann;");
                $dia_method->invoke($uml_class, array($value));
              } else {
                // ERROR
                Console::writeLine('ERROR');
              }
              break;

            case 'attribute':
              $fields= $class->getFields();
              usort(&$fields, create_function('$a, $b', 'return strcmp($a->getName(), $b->getName());'));
              foreach (array_keys($fields) as $key) {
                if (0 == strncmp('__', $fields[$key]->getName(), 2)) continue;
                if (!$class->equals($fields[$key]->getDeclaringClass())) continue;
                $dia_method->invoke($uml_class, array($fields[$key]));
              }
              break;

            case 'method':
              $methods= $class->getMethods();
              usort(&$methods, create_function('$a, $b', 'return strcmp($a->getName(), $b->getName());'));
              foreach (array_keys($methods) as $key) {
                if (0 == strncmp('__', $methods[$key]->getName(), 2)) continue;
                if (!$class->equals($methods[$key]->getDeclaringClass())) continue;
                $dia_method->invoke($uml_class, array($methods[$key]));
              }
              break;

            default:
              return throw(new IllegalArgumentException('Unknown annotation type: "'.$type.'"'));
          }
        }
        $layer->add($uml_class);

        // recurse
        $parent_class= &$class->getParentclass();
        if ($recurse > 0 and (NULL !== $parentclass)) {
          $parent_dia= DiaDiagram::constructFromClass($parentclass, --$recurse);
        }
      }

      return $dia;
    }


    /************************ Class Methods *****************************/

    /**
     *
     *
     */
    function addClass(&$classdoc) {

    }

    /**
     * Write Dia diagram to given filename or STDOUT
     */
    function write($filename= NULL) {}

    /**
     * Initialize this DiaDiagram with DiaData and DiaLayer
     *
     * @access  protected
     */
    function initialize() {
      $data= &new DiaData();
      $data->initialize();
      $this->add($data);
      $this->add(new DiaLayer('Background', TRUE));
    }

    /**
     * Add namespace declaration to root node
     *
     * @access  protected
     * @param   array ns
     */
    #[@xmlmapping(xpath = '@xmlns', type = 'array')]
    function addNamespace($ns) {
      $this->ns[]= &$ns;
    }

    /************************* Parent Functions *************************/

    /**
     * Return XML representation of DiaComposite
     *
     * @access  protected
     * @return  &xml.Node
     */
    function &getNode() {
      $node= &parent::getNode();
      while (list($ns, $url)= each($this->ns)) {
        $node->setAttribute('xmlns:'.$ns, $url);
      }
      return $node;
    }

    /**
     * @return  string XML representation of the DIAgramm
     */
    function getSource($indent= TRUE) {
      $Node= &$this->getNode();
      $Tree= &new Tree();
      $Tree->root= &$Node;
      return $Tree->getDeclaration()."\n".$Tree->getSource($indent);
    }

    /**
     * @param   string filename Filename to save the DIAgramm to
     */
    function saveTo($filename, $zip= FALSE) {
      // TODO: catch exceptions!
      uses('io.File');
      $File= &new File($filename);
      $File->open(FILE_MODE_WRITE);
      $File->write($this->getSource(FALSE)); // no indentation!
      $File->close();
      if ($zip) {
        system("gzip -f $filename && mv $filename.gz $filename");
      }
    }

  }
?>
