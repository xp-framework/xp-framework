<?php
/* This class is part of the XP framework
 *
 * $Id: DiaMarshaller.class.php 8894 2006-12-19 11:31:53Z kiesel $
 */

  uses(
    'util.cmd.Console',
    'text.doclet.RootDoc',
    'org.dia.DiaDiagram',
    'org.dia.DiaUMLClass',
    'org.dia.DiaUMLGeneralization',
    'org.dia.DiaUMLDependency',
    'org.dia.DiaUMLRealizes'
  );

  /**
   * Generator for 'dia' diagrams
   *
   * <code>
   *   $Dia= DiaMarshaller::marshal(array('util.Date'));
   *
   *   $Dia->getSource(); // complete XML string
   *   $Dia->getNode();   // instance of xml.Node
   *   $Dia->saveto('filename.dia'); // write to file (gzipped)
   * </code>
   *
   */
  class DiaMarshaller extends Object {
    public
      $_root= NULL,
      $_dia= NULL,
      $_layer= NULL,
      $_classnames= array(),
      $_classes= array(),
      $_deps= array(),
      $_imps= array(),
      $_gens= array(),
      $_class_ids= array();

    /**
     *
     */
    public static function getInstance() {
      static $Instance= NULL;

      if (!$Instance) {
        $Instance= new DiaMarshaller();
      }
      return $Instance;
    }

    /**
     * Turns any number of given classnames into a 'dia' diagram
     *
     * @param   array classnames An array containing fully qualified class names
     * @param   int recurse default 0 How many levels of recursion
     * @param   bool depend default FALSE Include dependencies
     * @return  org.dia.DiaDiagram
     */
    public static function marshal($classnames, $recurse= 0, $depend= FALSE) {
      $I= DiaMarshaller::getInstance();
      // initialize RootDoc
      $I->_root= new RootDoc();

      // initialize DiaDiagram
      $I->_dia= new DiaDiagram();
      $I->_dia->initialize();
      $Layers= $I->_dia->getChildByType('DiaLayer');
      $I->_layer= $Layers[0]; 

      /*
      method 1:
      - loop over array of classnames
      - add classnames during recursion...

      method 2:
      - loop over array of classnames
      - recurse directly
      */

      // initialize variables
      $I->_classes= array();
      $I->_classnames= $classnames;
      reset($I->_classnames); // reset array

      // process given classes
      while ($name= current($I->_classnames)) {
        $I->_recurse($name, $recurse, $depend);
        next($I->_classnames);
      }
      /*foreach (array_values($I->_classnames) as $name) {
        //if (isset($this->_classes[$name])) continue; // already processed
        $I->_recurse($name, $recurse, $depend);
      }*/

      Console::writeLine('Classes: '.xp::stringOf(array_keys($I->_classes)));
      Console::writeLine('Dependencies: '.xp::stringOf($I->_deps));
      Console::writeLine('Implementations: '.xp::stringOf($I->_imps));
      Console::writeLine('Generalizations: '.xp::stringOf($I->_gens));

      // generate and add classes to DiaDiagram
      foreach (array_keys($I->_classes) as $classname) {
        try {
          $Dia_class= $I->_genClass($I->_classes[$classname]);
        } catch (IllegalArgumentException $e) {
          $e->printStackTrace();
          exit(-1);
        }
        // save dia object-id by classname
        $I->_class_ids[$classname]= $Dia_class->getId();
        // add to DiaDiagram
        $I->_layer->addObject($Dia_class);
      }

      //Console::writeLine('IDs: '.xp::stringOf($I->_class_ids));

      // generate and add dependencies
      foreach (array_keys($I->_deps) as $from) {
        foreach ($I->_deps[$from] as $to) {
          // skip if there exists also a generalization or implementation
          if ($I->_gens[$from] === $to or in_array($to, $I->_imps[$from])) {
            // Console::writeLine("Skipping dependency: $from -> $to");
            continue;
          }
          // add to DiaDiagram
          $I->_layer->addObject($I->_genDependency($from, $to));
        }
      }
      // generate and add implementations
      foreach (array_keys($I->_imps) as $from) {
        foreach ($I->_imps[$from] as $to) {
          // skip if there exists also a generalization?
          if (in_array($to, $I->_gens[$from])) {
            // Console::writeLine("Skipping implementation: $from -> $to");
            continue;
          }
          // add to DiaDiagram
          $I->_layer->addObject($I->_genImplemenation($from, $to));
        }
      }
      // generate and add generalizations
      foreach (array_keys($I->_gens) as $from) {
        $to= $I->_gens[$from];
        // Console::writeLine("Gen: $from -> $to...");
        // add to DiaDiagram
        $I->_layer->addObject($I->_genGeneralization($from, $to));
      }

      return $I->_dia;
    }
    
    /**
     * Recursion method which collects all classnames, dependencies, interfaces
     * and generalizations.
     *
     * Calls itself for each additional class found
     *
     * @param   string classname Fully qualified class name
     * @param   int recurse The level of recursion
     * @param   bool depend Include dependencies (uses())
     */
    protected function _recurse($classname, $recurse, $depend) {
      if (isset($this->_classes[$classname])) {
        Console::writeLine("skipping $classname...");
        return;
      } else {
        Console::writeLine("processing $classname (recurse=$recurse, depend=$depend)...");
      }
      // get ClassDoc
      try {
        $Classdoc= $this->_root->classNamed($classname);
      } catch (IllegalArgumentException $e) {
        Console::writeLine("Class not found: $classname");
        exit(-1);
      } catch(XPException $e) {
        die('Unexpected exception: '.$e->toString());
      }
      // add classname to $this->_classes
      $this->_classes[$classname]= $Classdoc;

      // add dependencies
      if ($depend and $recurse >=0) {
        while ($Classdoc->usedClasses->hasNext()) {
          $Dependency= $Classdoc->usedClasses->next();
          // add dependency definition 
          $this->_deps[$classname][]= $Dependency->qualifiedName();
          // add dependency class
          if (!array_key_exists($Dependency->qualifiedName(), $this->_classes))
            $this->_recurse($Dependency->qualifiedName(), $recurse-1, $depend);
          //  $this->_classnames[]= $Dependency->qualifiedName();
        }
      }

      // add implemenations
      while ($Classdoc->interfaces->hasNext() and $recurse >=0 ) {
        $Interface= $Classdoc->interfaces->next();
        // add interface definition
        $this->_imps[$classname][]= $Interface->qualifiedName();
        // add interface class
        if (!array_key_exists($Interface->qualifiedName(), $this->_classes))
          $this->_recurse($Interface->qualifiedName(), $recurse-1, $depend);
        //   $this->_classnames[]= $Interface->qualifiedName();
      }

      // recurse
      if ($recurse > 0 and NULL !== ($Superdoc= $Classdoc->superclass)) {
        // add generalization definition
        $this->_gens[$classname]= $Superdoc->qualifiedName();
        // recurse parent class, only if not already processed
        if (!array_key_exists($Superdoc->qualifiedName(), $this->_classes)) {
          $this->_recurse($Superdoc->qualifiedName(), $recurse-1, $depend);
        //  $this->_classnames[]= $Superdoc->qualifiedName();
        }
      }
    }
    
    /**
     * Generates DiaUMLClass object for a single class - no recursion!
     *
     * @param   text.doclet.ClassDoc classdoc The ClassDoc instance of the class to generate
     * @return  org.dia.DiaUMLClass
     * @throws  lang.IllegalArgumentException If argument is not usable
     */
    protected function _genClass($classdoc) {
      // accept only ClassDoc
      if (!is('ClassDoc', $classdoc)) {
        throw new IllegalArgumentException('No ClassDoc given!');
      }
      $ClassDoc= $classdoc;
        
      // get DiaUMLClass
      $UMLClass= new DiaUMLClass();
      $DiaClass= $UMLClass->getClass();
      $DiaMethods= $DiaClass->getMethods();

      // loop over methods (annotations)
      for ($i= 0, $s= sizeof($DiaMethods); $i < $s; $i++) {
        $Method= $DiaMethods[$i];
        // Console::writeLine('Method: '.$Method->getName().' Annotations: '.xp::stringOf($Method->getAnnotations()));
        // skip methods that don't have the appropriate annotation
        if (!$Method->hasAnnotation('fromClass')) continue;
        if (!$Method->hasAnnotation('fromClass', 'type')) continue;

        // determine action according to 'type'
        $ann_type= $Method->getAnnotation('fromClass', 'type');
        // Console::writeLine('Method: '.$Method->getName()." Annotation-type: $ann_type");
        switch ($ann_type) {
          case 'string':
          case 'integer':
          case 'bool':
            if ($Method->hasAnnotation('fromClass', 'eval')) {
              $ann_eval= $Method->getAnnotation('fromClass', 'eval');
              // Console::write('Evaluating: ', $ann_eval);
              $value= eval("return $ann_eval;");
              // Console::writeLine('... Value: ', $value);
              try {
                $Method->invoke($UMLClass, array($value));
              } catch (Exception $e) {
                Console::writeLine('Fatal: ', $e->toString());
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
            /** TODO:
            $Attrib= &new DiaUMLAttribute($name, $value, $type); parameters???
            $Attrib->setName($name);
            if (isset($fields[$name])) {
              $Attrib->setValue($fields[$name]);
              $Attrib->setType(xp::typeOf($fields[$name]));
            }
            $Method->invoke($UMLClass, array($Attrib));
            */
            break;
          case 'method':
            $methods= $ClassDoc->methods;
            for ($i= 0, $s= sizeof($methods); $i < $s; $i++) {
              // skip magic methods?!? what about __construct() and __destruct()?
              if (0 == strncmp('__', $methods[$i]->name(), 2)) continue;
              // TODO: can we figure out in which class the method was defined?
              $Method->invoke($UMLClass, array($methods[$i]));
            }
            break;
          default:
            throw new IllegalArgumentException("Unknown annotation type: '$type'");
        }
      }

      return $UMLClass;
    }

    /**
     * Generates and returns a DiaUMLDependency $from class $to class
     *
     * @param   string from Fully qualified classname of the depending class
     * @param   string from Fully qualified classname of the depended class
     * @return  org.dia.DiaUMLDependecy
     */
    protected function _genDependency($from, $to) {
      $Dia_dep= new DiaUMLDependency();
      $Dia_dep->beginAt($this->_getObjectId($from));
      $Dia_dep->endAt($this->_getObjectId($to));
      return $Dia_dep;
    }

    /**
     * Generates and returns a DiaUMLRealizes $from class $to class
     *
     * @param   string from Fully qualified classname of the implementing class
     * @param   string to Fully qualified classname of the interface class
     * @return  org.dia.DiaUMLRealizes
     */
    protected function _genImplemenation($from, $to) {
      $Dia_imp= new DiaUMLRealizes();
      $Dia_imp->beginAt($this->_getObjectId($from));
      $Dia_imp->endAt($this->_getObjectId($to));
      return $Dia_imp;
    }

    /**
     * Generates and returns a DiaUMLGeneralization $from class $to class
     *
     * @param   string from Fully qualified classname of the child class
     * @param   string to Fully qualified classname of the parent class
     * @return  org.dia.DiaUMLGeneralization
     */
    protected function _genGeneralization($from, $to) {
      $Dia_gen= new DiaUMLGeneralization();
      $Dia_gen->beginAt($this->_getObjectId($from));
      $Dia_gen->endAt($this->_getObjectId($to));
      return $Dia_gen;
    }

    /**
     * Returns the DIAgram object ID of a fully qualified classname
     *
     * @param   string classname Fully qualified classname (i.e. 'util.Date')
     * @return  string
     */
    protected function _getObjectId($classname) {
      if (isset($this->_class_ids[$classname])) {
        return $this->_class_ids[$classname];
      }
      return NULL;
    }

  }
?>
