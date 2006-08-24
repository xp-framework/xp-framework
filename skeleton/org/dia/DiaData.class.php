<?php

  uses(
    'org.dia.DiaCompound'
  );

  class DiaData extends DiaCompound {

    var
      $node_name= 'dia:diagramdata';

    function initialize() {
      $this->add(new DiaAttribute('background', '#FFFFFF', 'color'));
      $this->add(new DiaAttribute('pagebreak', '#000099', 'color'));
      $paper= &new DiaAttribute('paper');
      $paper->add(new DiaComposite('paper'));
      $grid= &new DiaAttribute('grid');
      $grid->add(new DiaComposite('grid'));
      $guides= &new DiaAttribute('guides');
      $guides->add(new DiaComposite('guides'));
      $this->add($paper);
      $this->add($grid);
      $this->add($guides);
    }

  }
?>
