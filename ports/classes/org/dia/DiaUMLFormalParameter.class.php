<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaComposite'
  );

  /**
   * Represents a 'dia:composite type="umlformalparameter"' node
   *
   */
  class DiaUMLFormalParameter extends DiaComposite {

    var
      $type= 'umlformalparameter';

    function initialize() {
      // default values
      $this->setName('__noname__');
      $this->setType(NULL);
    }
 }
?>
