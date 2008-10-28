<?php
/*
 *
 * $Id$
 */

  uses('org.dia.DiaObject');

  /**
   * Base class for all 'dia:object type="UML -' nodes
   *
   * This class defines most - if not all - methods and annotations needed to
   * manipulate and generate the accroding diagram node
   *
   * TODO: Really necessary?
   *
   */
  class DiaUMLBaseObject extends DiaObject {

    public
      $type= NULL;

    public function __construct() {
      $valid_types= array(
        'Class', 'Note', 'Dependency', 'Realizes', 'Generalization',
        'Association', 'Implements'
        /* NOT YET IMPLEMENTED:
        'Constraint', 'SmallPackage',
        'LargePackage', 'Actor', 'Usecase', 'Lifeline', 'Object', 'Message',
        'Component', 'Component Feature', 'Node', 'Classicon', 'State Term',
        'State', 'Activity', 'Branch', 'Fork', 'Transition'*/
      );
    }

    public function initialize() {
    }
  }
?>
