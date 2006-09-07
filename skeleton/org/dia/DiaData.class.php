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
      $children= array(),
      $node_name= 'dia:diagramdata';

    /**
     * Initialize the compound element with default values
     */
    function initialize() {
      $this->set('background', new DiaAttribute('background', '#FFFFFF', 'color'));
      $this->set('pagebreak', new DiaAttribute('pagebreak', '#000099', 'color'));
      // color (of grid?)
      $this->set('color', new DiaAttribute('color', '#d8e5e5', 'color'));
      // add paper
      $paper= &new DiaAttribute('paper');
      $paper->set('paper', new DiaComposite('paper'));
      $this->set('paper', $paper);
      // grid
      $grid= &new DiaAttribute('grid');
      $grid->set('paper', new DiaComposite('grid'));
      $this->set('grid', $grid);
      // guides
      $guides= &new DiaAttribute('guides');
      $guides->set('guides', new DiaComposite('guides'));
      $this->set('guides', $guides);
    }


    /**
     *
     *
     * @param   string color Example: '#FFFFFF'
     */
    #[@fromDia(xpath= 'dia:attribute[@name="background"]/dia:color/@val', value= 'string')] 
    function setBackground($color) {
      $this->set('background', new DiaAttribute('background', $color, 'color'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="pagebreak"]/dia:color/@val', value= 'string')]
    function setPagebreak($color) {
      $this->set('pagebreak', new DiaAttribute('pagebreak', $color, 'color'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="color"]/dia:color/@val', value= 'string')]
    function setColor($color) {
      $this->set('color', new DiaAttribute('color', $color, 'color'));
    }

    #[@fromDia(xpath= 'dia:attribute[@name="paper"]', class= 'org.dia.DiaPaper')]
    function setPaper(&$Paper) {
      $this->set('paper', $Paper);
    }

    #[@fromDia(xpath= 'dia:attribute[@name="grid"]', class= 'org.dia.DiaGrid')]
    function setGrid(&$Grid) {
      $this->set('grid', $Grid);
    }

    #[@fromDia(xpath= 'dia:attribute[@name="guides"]', class= 'org.dia.DiaGuides')]
    function setGuides(&$Guides) {
      $this->set('guides', $Guides);
    }

  }
?>
