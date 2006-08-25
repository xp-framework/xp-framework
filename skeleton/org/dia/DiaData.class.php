<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaCompound'
  );

  /**
   * Representation of a 'dia:diagramdata' node
   *
   */
  class DiaData extends DiaCompound {

    var
      $node_name= 'dia:diagramdata';

    /**
     * Initialize the compound element
     */
    function initialize() {
      // background
      $this->add(new DiaAttribute('background', '#FFFFFF', 'color'));
      // pagebreak
      $this->add(new DiaAttribute('pagebreak', '#000099', 'color'));
      // add paper
      $paper= &new DiaAttribute('paper');
      $paper->add(new DiaComposite('paper'));
      // grid
      $grid= &new DiaAttribute('grid');
      $grid->add(new DiaComposite('grid'));
      // guides
      $guides= &new DiaAttribute('guides');
      $guides->add(new DiaComposite('guides'));

      // add all of the above
      $this->add($paper);
      $this->add($grid);
      $this->add($guides);
    }

  }
?>
