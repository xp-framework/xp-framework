<?php
/*
 *
 * $Id:$
 */

  uses(
    'util.cmd.Console',
    'text.doclet.RootDoc',
    'org.dia.DiaDiagram',
    'org.dia.DiaUMLGeneralization',
    'org.dia.DiaUMLDependency',
    'org.dia.DiaUMLRealizes'
  );

  /**
   * Generator for 'dia' diagrams
   *
   * <code>
   *   try (); {
   *     $Dia= &DiaMarshaller::marshal(array('util.Date'));
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   $Dia->getSource(); // complete XML string
   *   $Dia->getNode();   // instance of xml.Node
   *   $Dia->saveto('filename.dia'); // write to file (gzipped)
   * </code>
   *
   */
  class DiaMarshaller extends Object {

    var
      $root= NULL,        // RootDoc instance
      $dia= NULL,         // DiaDiagram instance
      $layer= NULL,       // DiaLayer instance
      $classes= array();

    function &getInstance() {
      static $Instance= NULL;

      if (!$Instance) {
        $Instance= new DiaMarshaller();
      }
      return $Instance;
    }

    /**
     * Turns any number of given classnames into a 'dia' diagram
     *
     * @model   static
     * @param   array classnames An array containing fully qualified class names
     * @param   int recurse default 0 How many levels of recursion
     * @param   bool depend default FALSE Include dependencies
     * @return  &org.dia.DiaDiagram
     */
    function &marshal($classnames, $recurse= 0, $depend= FALSE) {
      $I= &DiaMarshaller::getInstance();
      // initialize RootDoc
      $I->root= &new RootDoc();

      // initialize DiaDiagram
      $I->dia= &new DiaDiagram();
      $I->dia->initialize();
      $Layers= $I->dia->getChildByType('DiaLayer');
      $I->layer= &$Layers[0]; 

      // process given classes
      $I->classes= array();
      foreach (array_values($classnames) as $name) {
        $I->recurse($name, $recurse, $depend);
      }

      return $I->dia;
    }
    
    /**
     * Start the marshalling
     * TODO: real recursion!
     *
     * @param   string name Fully qualified class name
     * @param   int recurse default 0 The level of recursion
     * @param   bool depend default FALSE Include dependencies (uses())
     * @return  bool?
     */
    function recurse($name, $recurse= 0, $depend= FALSE) {
      // class already processed?
      if (isset($this->classes[$name])) return;
    
      // get ClassDoc object by name
      try (); {
        $Classdoc= &$this->root->classNamed($name);
      } if (catch('IllegalArgumentException', $e)) {
        Console::writeLine("Class not found: $name");
        exit(-1);
      } elseif (catch('Exception', $e)) {
        die('Unexpected exception: '.$e->toString());
      }

      // generate and add DiaUMLClass to DiaLayer
      $Dia_topclass= &$this->_genClass($Classdoc);
      $this->layer->add($Dia_topclass);
      $this->classes[$name]= $Dia_topclass->getId();

      // add implementations (interfaces) to diagram
      while ($Classdoc->interfaces->hasNext()) {
        $ImpClass= &$Classdoc->interfaces->next();
        $imp_id= $this->classes[$ImpClass->qualifiedName()];

        // create UML realization object
        $Dia_imp= &new DiaUMLRealizes();
        $Dia_imp->beginAt($Dia_topclass->getId());

        if (isset($imp_id)) {
          // just set endpoint of realization
          $Dia_imp->endAt($imp_id);
        } else {
          // create and add class
          $Dia_class= &$this->_genClass($ImpClass);
          $this->layer->add($Dia_class);
          $this->classes[$ImpClass->qualifiedName()]= $Dia_class->getId();
          // set endpoint of realizes
          $Dia_imp->endAt($Dia_class->getId());
        }

        // add realization object
        $this->layer->add($Dia_imp);
      }

      // add dependencies to diagram
      if ($depend) {
        while ($Classdoc->usedClasses->hasNext()) {
          // get depending class and name
          $DepClass= &$Classdoc->usedClasses->next();
          $dep_id= $this->classes[$DepClass->qualifiedName()];

          // skip dependency if this is the parent class or implemented class! 
          // (already has generalization line)
          if ($DepClass->qualifiedName() === $Classdoc->superclass->qualifiedName()) continue;
          if (array_key_exists($DepClass->qualifiedName(), $Classdoc->interfaces->classes)) continue;

          // create UML dependency object
          $Dia_dep= &new DiaUMLDependency();
          $Dia_dep->beginAt($Dia_topclass->getId());
          
          if (isset($dep_id)) {
            // just set endpoint of dependency
            $Dia_dep->endAt($dep_id);
          } else {
            // generate and add class
            $Dia_class= &$this->_genClass($DepClass);
            $this->layer->add($Dia_class);
            $this->classes[$DepClass->qualifiedName()]= $Dia_class->getId();
            // set endpoint of dependency
            $Dia_dep->endAt($Dia_class->getId());
          }

          // add dependency object
          $this->layer->add($Dia_dep);
        }
      }

      // process recursion
      // reset variables
      $Class= &$Classdoc;
      $Dia_class= &$Dia_topclass;
      while ($recurse > 0) {
        // get super class if possible
        if (
          NULL === ($SuperClass= &$Class->superclass) or
          $SuperClass->qualifiedName() === $Class->qualifiedName()
        ) break;

        // create UML generalization object
        $Dia_gen= &new DiaUMLGeneralization();
        $Dia_gen->beginAt($Dia_class->getId());

        // check if superclass was already added
        $super_id= $this->classes[$SuperClass->qualifiedName()];
        if (isset($super_id)) {
          // just set endpoint of UML generalization
          $Dia_gen->endAt($super_id);
        } else {
          // generate and add class
          $Dia_class= &$this->_genClass($SuperClass);
          $this->layer->add($Dia_class);
          $this->classes[$SuperClass->qualifiedName()]= $Dia_class->getId();
          // set endpoint of UML generalization
          $Dia_gen->endAt($Dia_class->getId());
        }

        // add generalization object
        $this->layer->add($Dia_gen);
        $Class= &$SuperClass;
        $recurse--;
      }
    }

    /**
     * Generates DiaUMLClass object for a single class - no recursion!
     *
     * @model   static
     * @param   &text.doclet.ClassDoc classdoc
     * @return  &org.dia.DiaUMLClass
     * @throws  lang.IllegalArgumentException If argument is not usable
     */
    function &_genClass(&$classdoc) {
      // accept only ClassDoc
      if (!is('ClassDoc', $classdoc)) {
        return throw(new IllegalArgumentException('No ClassDoc given!'));
      }
      $ClassDoc= &$classdoc;
        
      // get DiaUMLClass
      $UMLClass= &new DiaUMLClass('UML - Class', 0);
      $DiaClass= &$UMLClass->getClass();
      $DiaMethods= $DiaClass->getMethods();

      // loop over methods (annotations)
      for ($i= 0, $s= sizeof($DiaMethods); $i < $s; $i++) {
        $Method= &$DiaMethods[$i];
        // skip methods that don't have the appropriate annotation
        if (!$Method->hasAnnotation('fromClass')) continue;
        if (!$Method->hasAnnotation('fromClass', 'type')) continue;

        // determine action according to 'type'
        $ann_type= $Method->getAnnotation('fromClass', 'type');
        switch ($ann_type) {
          case 'string':
          case 'integer':
          case 'bool':
            if ($Method->hasAnnotation('fromClass', 'eval')) {
              $ann_eval= $Method->getAnnotation('fromClass', 'eval');
              // Console::write('Evaluating: ', $ann_eval);
              $value= eval("return $ann_eval;");
              // Console::writeLine('... Value: ', $value);
              try (); {
                $Method->invoke($UMLClass, array($value));
              } if (catch('Exception', $e)) {
                Console::writeLine('Fatal Exception:', $e->toString());
                exit(-1);
              }
            } else {
              Console::writeLine('Fatal ERROR: Annotation "fromClass" has no attribute "eval"!');
              exit(-1);
            }
            break;
          case 'attribute':
            $fields= $ClassDoc->fields;
            foreach (array_keys($fields) as $name) {
              if (0 == strncmp('__', $name, 2)) continue; // skip magic fields
              // TODO: can we figure out in which class the attribute was defined?
              $Method->invoke($UMLClass, array(array($name => $fields[$name])));
            }
            break;
          case 'method':
            $methods= $ClassDoc->methods;
            for ($i= 0, $s= sizeof($methods); $i < $s; $i++) {
              // skip magic methods
              if (0 == strncmp('__', $methods[$i]->name(), 2)) continue;
              // TODO: can we figure out in which class the method was defined?
              $Method->invoke($UMLClass, array($methods[$i]));
            }
            break;
          default:
            return throw(new IllegalArgumentException("Unknown annotation type: '$type'"));
        }
      }

      return $UMLClass;
    }

  }
?>
