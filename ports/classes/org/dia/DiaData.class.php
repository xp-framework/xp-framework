<?php
/*
 *
 * $Id$
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

    public
      $node_name= 'dia:diagramdata';

    /**
     * Constructor: simply calls 'initialize()'
     *
     */
    public function __construct() {
      $this->initialize();
    }

    /**
     * Initialize this Data object with default values
     *
     */
    public function initialize() {
      // default values
      $this->setBackgroundColor('#FFFFFF');
      $this->setPagebreakColor('#000099');
      $this->setGridColor('#d8e5e5');
      // add paper
      $this->set('paper', new DiaAttribute('paper'));
      $this->setPaper(new DiaPaper());
      // add grid
      $this->set('grid', new DiaAttribute('grid'));
      $this->setGrid(new DiaGrid());
      // add guides
      $this->set('guides', new DiaAttribute('guides'));
      $this->setGuides(new DiaGuides());
    }

    /**
     * Returns the background color
     *
     * @return  string
     */
    public function getBackgroundColor() {
      return $this->getChildValue('background');
    }

    /**
     * Set the background color of the diagram
     *
     * @param   string color Example: '#FFFFFF'
     */
    #[@fromDia(xpath= 'dia:attribute[@name="background"]/dia:color/@val', value= 'string')] 
    public function setBackgroundColor($color) {
      $this->setColor('background', $color);
    }

    /**
     * Returns the pagebreak color
     *
     * @return  string
     */
    public function getPagebreakColor() {
      return $this->getChildValue('pagebreak');
    }

    /**
     * Set the pagebreak color of the diagram
     *
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="pagebreak"]/dia:color/@val', value= 'string')]
    public function setPagebreakColor($color) {
      $this->setColor('pagebreak', $color);
    }

    /**
     * Returns the grid color
     *
     * @return  string
     */
    public function getGridColor() {
      return $this->getChildValue('color');
    }

    /**
     * Set the grid color
     *
     * @param   string color
     */
    #[@fromDia(xpath= 'dia:attribute[@name="color"]/dia:color/@val', value= 'string')]
    public function setGridColor($color) {
      $this->setColor('color', $color);
    }

    /**
     * Returns the DiaPaper object
     *
     * @return  &org.dia.DiaPaper
     */
    public function getPaper() {
      return $this->getChild('paper');
    }

    /**
     * Set the Paper node
     *
     * @param   &org.dia.DiaPaper Paper
     */
    #[@fromDia(xpath= 'dia:attribute[@name="paper"]', class= 'org.dia.DiaPaper')]
    public function setPaper($Paper) {
      $Paper_node= $this->getChild('paper');
      $Paper_node->set('paper', $Paper);
    }

    /**
     * Returns the DiaGrid object   
     *
     * @return  &org.dia.DiaGrid
     */
    public function getGrid() {
      return $this->getChild('grid');
    }

    /**
     * Set the Grid node
     *
     * @param   &org.dia.DiaGrid Grid
     */
    #[@fromDia(xpath= 'dia:attribute[@name="grid"]', class= 'org.dia.DiaGrid')]
    public function setGrid($Grid) {
      $Grid_node= $this->getChild('grid');
      $Grid_node->set('grid', $Grid);
    }

    /**
     * Returns the DiaGuides object
     *
     * @return  &org.dia.DiaGuides
     */
    public function getGuides() {
      return $this->getChild('guides');
    }

    /**
     * Set the Guides node
     *
     * @param   &org.dia.DiaGuides Guides
     */
    #[@fromDia(xpath= 'dia:attribute[@name="guides"]', class= 'org.dia.DiaGuides')]
    public function setGuides($Guides) {
      $Guides_node= $this->getChild('guides');
      $Guides_node->set('guides', $Guides);
    }

  }
?>
