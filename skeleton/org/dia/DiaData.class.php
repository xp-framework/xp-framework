<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaCompound',
    'org.dia.DiaAttribute',
    'org.dia.DiaPaper',
    'org.dia.DiaGrid',
    'org.dia.DiaGuides'
  );

  /**
   * Representation of a 'dia:diagramdata' node. Contains information about the
   * paper size and orientation, the grid lines, and the guides.
   *
   */
  class DiaData extends DiaCompound {

    var
      $children= array(),
      $node_name= 'dia:diagramdata';

    /**
     * Initialize this Data object with default values
     *
     * @access  public
     */
    function initialize() {
      $this->setColor('background', '#FFFFFF');
      $this->setColor('pagebreak', '#000099');
      $this->setColor('color', '#d8e5e5');
      // add paper
      $this->set('paper', new DiaAttribute('paper'));
      $this->setPaper(new DiaPaper());
      // grid
      $this->set('grid', new DiaAttribute('grid'));
      $this->setGrid(new DiaGrid());
      // guides
      $this->set('guides', new DiaAttribute('guides'));
      $this->setGuides(new DiaGuides());
    }

    /**
     * Set the background color of the diagram
     *
     * @access  public
     * @param   string color Example: '#FFFFFF'
     */
    #[@fromDia(xpath= 'dia:attribute[@name="background"]/dia:color/@val', value= 'string')] 
    function setBackground($color) {
      $this->setColor('background', $color);
    }

    /**
     * Set the pagebreak color of the diagram
     *
     * @access public
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="pagebreak"]/dia:color/@val', value= 'string')]
    function setPagebreak($color) {
      $this->set('pagebreak', new DiaAttribute('pagebreak', $color, 'color'));
    }

    /**
     * Set the grid color
     *
     * @access  public
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="color"]/dia:color/@val', value= 'string')]
    function setColor($color) {
      $this->set('color', new DiaAttribute('color', $color, 'color'));
    }

    /**
     * Set the Paper node
     *
     * @access  public
     * @param   &org.dia.DiaPaper Paper
     */
    #[@fromDia(xpath= 'dia:attribute[@name="paper"]', class= 'org.dia.DiaPaper')]
    function setPaper(&$Paper) {
      $Paper_node= &$this->getChild('paper');
      $Paper_node->set('paper', $Paper);
    }

    /**
     * Set the Grid node
     *
     * @access  public
     * @param   &org.dia.DiaGrid Grid
     */
    #[@fromDia(xpath= 'dia:attribute[@name="grid"]', class= 'org.dia.DiaGrid')]
    function setGrid(&$Grid) {
      $Grid_node= &$this->getChild('grid');
      $Grid_node->set('grid', $Grid);
    }

    /**
     * Set the Guides node
     *
     * @access  public
     * @param   &org.dia.DiaGuides Guides
     */
    #[@fromDia(xpath= 'dia:attribute[@name="guides"]', class= 'org.dia.DiaGuides')]
    function setGuides(&$Guides) {
      $Guides_node= &$this->getChild('guides');
      $Guides_node->set('guides', $Guides);
    }

  }
?>
