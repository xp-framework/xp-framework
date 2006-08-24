<?php
/*
 *
 * $Id:$
 */

  /**
   *
   * <code>
   *   try (); {
   *     $uml_class= &DiaMarshaller::marshal(XPClass::forName('util.Date'));
   *     // OR: &DiaMarshaller::marshal('util.Date')
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   $Dia->getSource(); // XML string
   *   $Dia->getNode();   // Instance of xml.Node
   *   $Dia->saveto('filename.dia');    // write to file
   * </code>
   *
   */
  class DiaMarshaller extends Object {

    /**
     *
     *
     * @model   static
     * @param   &text.doclet.ClassDoc classdoc
     * @return  &org.dia.DiaUMLClass
     * @throws  lang.IllegalArgumentException If argument is not usable
     */
    function &marshal(&$classdoc) {
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
      for ($i=0, $s=sizeof($DiaMethods); $i < $s; $i++) {
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
              //Console::write('Evaluating: ', $ann_eval);
              $value= eval("return $ann_eval;");
              //Console::writeLine('... Value: ', $value);
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
              if (0 == strncmp('__', $methods[$i]->name(), 2)) continue; // skip magic methods
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
