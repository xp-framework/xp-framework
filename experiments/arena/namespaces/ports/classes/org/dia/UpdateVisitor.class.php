<?php
/*
 *
 * $Id: UpdateVisitor.class.php 8894 2006-12-19 11:31:53Z kiesel $
 */

  namespace org::dia;
  ::uses(
    'util.Visitor',
    'text.doclet.RootDoc'
  );

  define('UDT_VIS_DEBUG', FALSE);

  /**
   * Visitor that runs over a diagram structure and updates or adds classes
   *
   */
  class UpdateVisitor extends lang::Object implements util::Visitor {

    public
      $RootDoc= NULL,
      $Dia= NULL,
      $add= FALSE,
      $update= FALSE,
      $existing_classes= array(),
      $updated_classes= array(),
      $added_classes= array(),
      $classdocs= array();
    
    /**
     * Instantiates an UpdateVisitor with a list of classnames and two
     * options to add missing or update all existing classes in the diagram
     *
     * @param   string[] classnames List of fully qualified class names
     * @param   bool add default FALSE Add classes that don't exist in the diagram
     * @param   bool update default FALSE Update all classes found in the diagram
     */
    public function __construct($classnames, $add= FALSE, $update= FALSE) {
      $this->RootDoc= new text::doclet::RootDoc();
      $this->classdocs= array();
      $this->add= $add;
      $this->update= $update;

      if (empty($classnames)) {
        // no classnames given
        if (!$update) {
          util::cmd::Console::writeLine(
            "No classnames given and you did not specify 'update_all=TRUE': nothing to do!"
          );
          exit(-1);
        }
        if ($add) {
          util::cmd::Console::writeLine(
            "No classnames given to add! Just updating existing classes..."
          );
        }
      }

      // check given classnames and create ClassDoc for each class
      foreach ($classnames as $name) {
        try {
          $Classdoc= $this->RootDoc->classNamed($name);
        } catch (lang::IllegalArgumentException $e) {
          $e->printStackTrace();
          util::cmd::Console::writeLine("Class '$name' could not be found or parsed!");
          exit(-1);
        }
        $this->classdocs[$Classdoc->qualifiedName()]= $Classdoc;
      }

      if (UDT_VIS_DEBUG) util::cmd::Console::writeLine('UpdateVisitor: classes='.sizeof($classnames).' add='.$add.' update='.$update);
    }

    /**
     * Finalizes the visitor: adds missing classes and prints summary
     *
     */
    public function finalize() {
      // classes that are missing in the diagram
      $tobe_added= array_diff(array_keys($this->classdocs), $this->existing_classes);

      // add missing classes?
      if ($this->add) {
        foreach ($tobe_added as $classname) {
          try {
            $ClassDoc= $this->RootDoc->classNamed($classname);
          } catch (lang::IllegalArgumentException $e) {
            util::cmd::Console::writeLine("Class '$classname' could not be found or parsed!");
            exit(-1);
          }
          $this->_addClass($ClassDoc);
        }
      } elseif (!empty($tobe_added)) {
        util::cmd::Console::writeLine('The following classes were NOT added to the diagram:');
        util::cmd::Console::writeLine(implode(', ', $tobe_added));
      }

      // print summary
      util::cmd::Console::writeLine("==== UpdateVisitor summary: ====");
      util::cmd::Console::writeLine("Existing classes: ", implode(', ', $this->existing_classes));
      util::cmd::Console::writeLine("Updated classes: ", implode(', ', $this->updated_classes));
      util::cmd::Console::writeLine("Added classes: ", implode(', ', $this->added_classes));

      util::cmd::Console::writeLine('UpdateVisitor used up '.round(memory_get_usage()/1024, 2).' Kb of memory.');
    }

    /**
     * Visitor method: visits the DiaDiagram object structure and processes all
     * DiaUMLClass objects
     *
     * @param   &org.dia.DiaComponent Comp
     */
    public function visit($Comp) {
      // save reference to 
      if (is('org.dia.DiaDiagram', $Comp)) {
        $this->Dia= $Comp;
      }

      // only process DiaUMLClass components
      if (is('org.dia.DiaUMLClass', $Comp)) {
        $name= $Comp->getName();
        // collect all existing UMLClass names
        $this->existing_classes[]= $name;

        // update existing classes?
        if (!isset($this->classdocs[$name]) and $this->update) {
          // update: create ClassDoc and add it to $this->classdocs
          try {
            $ClassDoc= $this->RootDoc->classNamed($name);
          } catch (lang::IllegalArgumentException $e) {
            $e->printStackTrace();
            util::cmd::Console::writeLine("Class '$name' could not be found or parsed!");
            exit(-1);
          } catch(lang::XPException $e) {
            $e->printStackTrace();
            util::cmd::Console::writeLine('Caught unknown exception!');
            exit(-1);
          }
          $this->classdocs[$name]= $ClassDoc;
        }

        // update the UMLClass in the diagram
        if (isset($this->classdocs[$name]))
          $this->_updateClass($Comp, $this->classdocs[$name]);
      }
    }

    /**
     * Returns true if the visitor has changed classes of the diagram
     * (update_classes is not empty
     *
     * @return  bool
     */
    public function changedClasses() {
      if (!empty($this->updated_classes)) return TRUE;
      return FALSE;
    }

    /**
     * Updates the given UMLClass of the diagram according to the given
     * ClassDoc
     *
     * @param   &org.dia.DiaUMLClass Class
     * @param   &text.doclet.ClassDoc ClassDoc
     */
    protected function _updateClass($Class, $ClassDoc) {
      if (UDT_VIS_DEBUG) util::cmd::Console::writeLine('* Updating class '.$ClassDoc->qualifiedName().'...');
      /*
      ClassDoc vs. DiaUMLClass...
      1) delete elements from DiaUMLClass that don't exist in ClassDoc
      2) loop over ClassDoc elements
      3) check if element exists in DiaUMLClass
        - no: add
        - yes: update if necessary
      */

      // get class attributes and methods
      $attributes= $ClassDoc->fields;
      $meths= $ClassDoc->methods;
      foreach (array_keys($meths) as $key) {
        $methods[$meths[$key]->name()]= $meths[$key];
      }

      if (UDT_VIS_DEBUG) util::cmd::Console::writeLine('  Class has '.sizeof($attributes).' attributes...');
      // loop over UMLClass attributes (delete)
      $Attributes_node= $Class->getChild('attributes');
      $dia_attributes= $Attributes_node->getChildByType('org.dia.DiaUMLAttribute');
      foreach (array_keys($dia_attributes) as $key) {
        $name= $dia_attributes[$key]->getName();
        if (!isset($attributes[$name])) {
          $Attributes_node->remChild($dia_attributes[$key]);
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("Attribute '$name': deleted...");
          continue;
        }
        $uml_attributes[$name]= $dia_attributes[$key];
      }

      // loop over class attributes (update/add)
      foreach (array_keys($attributes) as $name) {
        if (isset($uml_attributes[$name])) {
          // dia attributes exists: check for update
          if (isset($attributes[$name])) {
            // value is set
            if ($uml_attributes[$name]->getValue() !== $attributes[$name]) {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  -> Attribute '$name': updated...");
              $uml_attributes[$name]->setValue($attributes[$name]);
            } else {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  -> Attribute '$name': already up-to-date...");
            }
          } else { 
            // value not set
            if ($uml_attributes[$name]->getValue() !== '') {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  -> Attribute '$name': value of diagram takes precedence...");
            } else {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  -> Attribute '$name': value not set!");
            }
          }
        } else {
          // dia attribute doesn't exist: add
          $Attribute= new DiaUMLAttribute();
          $Attribute->setName($name);

          $value= $attributes[$name];
          if (isset($value)) {
            $type= ::xp::typeOf($value);
          } else {
            $type= NULL;
            $value= 'NULL';
          }
          $Attribute->setValue($value);
          if (isset($type)) $Attribute->setType($type);

          // name begins with '_'
          if (0 == strncmp('_', substr($name, 1), 1))
            $Attribute->setVisibility(2);
          if (0 == strncmp('__', $name, 2))
            $Attribute->setVisibility(1);

          $Class->addUMLAttribute($Attribute);
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  -> Attribute '$name': added...");
        }
      }

      if (UDT_VIS_DEBUG) util::cmd::Console::writeLine('  Class has '.sizeof($methods).' methods...');
      // loop over UMLClass methods (delete)
      $Operations_node= $Class->getChild('operations');
      $dia_operations= $Operations_node->getChildByType('org.dia.DiaUMLMethod');
      foreach (array_keys($dia_operations) as $key ) {
        $name= $dia_operations[$key]->getName();
        if (!isset($methods[$name])) {
          $Operations_node->remChild($dia_operations[$key]);
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  => Method '$name': deleted...");
          continue;
        }
        $uml_methods[$name]= $dia_operations[$key];
      }

      // loop over class methods (update/add)
      foreach (array_keys($methods) as $name) {
        if (isset($uml_methods[$name])) {
          // dia method exists: check for update
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  => Updating existing method '$name'...");
          // ==> flags
          $Method= $uml_methods[$name];

          // update return type
          $return_tags= $methods[$name]->tags('return');
          if (!empty($return_tags)) {
            if ($Method->getType() !== $return_tags[0]->type) {
              $Method->setType($return_tags[0]->type);
            }
          }
          
          // update visibility
          // -> doesn't make sense, because it's determined by the method name

          // update comment
          if (($comment= $methods[$name]->commentText()) !== $Method->getComment())
            $Method->setComment($comment);

          // update model (abstract/static)
          $model_tags= $methods[$name]->tags('model');
          if (!empty($model_tags)) {
            // set defaults
            $abstract= FALSE;
            $class_scope= FALSE;
            // loop over @model tags
            foreach (array_keys($model_tags) as $key) {
              if ($model_tags[$key]->text() === 'abstract')
                $abstract= TRUE;
              if ($model_tags[$key]->text() === 'static')
                $class_scope= TRUE;
            }
            // update if different
            if ($Method->getAbstract() !== $abstract)
              $Method->setAbstract($abstract);
            if ($Method->getClassScope() !== $class_scope) 
              $Method->setClassScope($class_scope);
          }

          // ==> parameters
          $Params_node= $Method->getChild('parameters');
          $Params= $Params_node->getChildren();
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("Params: ".::xp::stringOf(array_keys($Params)));
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("Args: ".::xp::stringOf(array_keys($methods[$name]->arguments)));

          // no arguments? delete existing parameters in diagram
          if (empty($methods[$name]->arguments)) {
            if (!empty($Params)) {
              // overwrite 'parameters' node with an empty one
              $Params_node= new DiaAttribute('parameters');
              //=$Method->set('parameters', new DiaAttribute('parameters'));
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> Parameters got deleted...");
            } else {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> Parameters are up-to-date...");
            }
          } else {
            // compare arguments and parameters one-by-one
            foreach (array_keys($methods[$name]->arguments) as $param_name) {
              if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> checking argument '$param_name'...");
              if (!isset($Params[$param_name]) or $Params[$param_name]->getName() !== $param_name) {
                // replace/add parameter completely
                if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> TODO:REPLACE parameter: $param_name...");
              } else {
                // update parameter (name matches)
                if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> TODO:UPDATE parameter: $param_name...");
              }
              $i++;
            }
          }
          
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  => Method '$name' updated...");
        } else {
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  => Adding Method '$name'...");
          // dia method does't exist: add
          $Method= new DiaUMLMethod();
          $Method->setName($name);

          // return type
          $return_tags= $methods[$name]->tags('return');
          if (!empty($return_tags)) {
            $Method->setType($return_tags[0]->type);
          }

          // visibility
          if (0 == strncmp('_', $name, 1))
            $Method->setVisibility(2);
          if (0 == strncmp('__', $name, 2))
            $Method->setVisibility(1);

          // comment
          $Method->setComment($methods[$name]->commentText());

          // model (abstract/static)
          $model_tags= $methods[$name]->tags('model');
          if (!empty($model_tags)) {
            foreach (array_keys($model_tags) as $key) {
              if ($model_tags[$key]->text() === 'abstract')
                $Method->setAbstract(TRUE);
              if ($model_tags[$key]->text() === 'static')
                $Method->setClassScope(TRUE);
            }
          }

          // create method parameters
          // WEIRD: if the following is renamed to $Params it doesn't work anymore!
          $Params_node= $Method->getChild('parameters');
          // loop over arguments
          foreach (array_keys($methods[$name]->arguments) as $param_name) {
            if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("    -> Adding parameter '$param_name'...");
            $Param= new DiaUMLMethodParameter();
            $Param->setName($param_name);

            // type
            $value= $methods[$name]->arguments[$param_name];
            $type= NULL;
            if (isset($value)) {
              $evalue= eval("return $value;");
              if (isset($evalue)) 
                $type= ::xp::typeOf($evalue);
            }
            $Param->setValue($value);
            $Param->setType($type);

            // add to 'parameters' node
            $Params_node->set($param_name, $Param);
          }

          $Class->addUMLMethod($Method);
          if (UDT_VIS_DEBUG) util::cmd::Console::writeLine("  => Method '$name': added...");
        }
      }

      $this->updated_classes[]= $ClassDoc->qualifiedName();
    }

    /**
     * Adds the given class to the 'Background' layer of the diagram
     *
     * @param   &text.doclet.ClassDoc ClassDoc
     */
    protected function _addClass($ClassDoc) {
      // check for DiaDiagram instance
      if (!isset($this->Dia)) {
        util::cmd::Console::writeLine('DiaDiagram object not defined!');
        exit(-1);
      }
      // get background layer
      $Layer= $this->Dia->getLayer('Background');

      // create new (empty) UMLClass and update it
      $Class= new DiaUMLClass();
      $Class->setName($ClassDoc->qualifiedName());
      $this->_updateClass($Class, $ClassDoc);

      // add the class to the background layer
      $Layer->addClass($Class);

      $this->added_classes[]= $ClassDoc->qualifiedName();
    }

  } 
?>
