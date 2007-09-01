<?php
/*
 *
 * $Id:$
 */

  namespace org::dia;

  ::uses(
    'org.dia.DiaComposite'
  );

  /**
   * Represents one of two roles of an UMLAssociation
   *
   */
  class DiaRole extends DiaComposite {

    public
      $type= NULL; 

    /**
     * Initializes this Role object with default values
     *
     */
    public function initialize() {
      // default values
      $this->setRole();
      $this->setMultiplicity('');
      $this->setArrow(FALSE);
      $this->setAggregate(0);
      $this->setVisibility(0);
    }

    /**
     * Sets the Role name
     *
     * @param   string name
     */
    #[@fromDia(xpath= 'dia:attribute[@name="role"]/dia:string', value= 'string')]
    public function setRole($name) {
      $this->setString('role', $name);
    }

    /**
     * Sets 'multiplicity' string of the role
     *
     * @param   string multi
     */
    #[@fromDia(xpath= 'dia:attribute[@name="multiplicity"]/dia:string', value= 'string')]
    public function setMultiplicity($multi) {
      $this->setString('multiplicity', $multi);
    }

    /**
     * Show an arrow for this Role?
     *
     * @param   bool arrow
     */
    #[@fromDia(xpath= 'dia:attribute[@name="arrow"]/dia:boolean/@val', value= 'boolean')]
    public function setArrow($arrow) {
      $this->setBoolean('arrow', $arrow);
    }

    /**
     * Marks this side of the Role as aggregate or composite
     *
     * @param   int value
     */
    #[@fromDia(xpath= 'dia:attribute[@name="aggregate"]/dia:enum/@val', value= 'int')]
    public function setAggregate($value) {
      $this->setEnum('aggregate', $value);
    }

    /**
     * Sets the 'visbility' of this Role object
     *
     * @param   int visibility
     */
    #[@fromDia(xpath= 'dia:attribute[@name="visibility"]/dia:enum/@val', value= 'int')]
    public function setVisibility($visibility) {
      $this->setEnum('visibility', $visibility);
    }
 }
?>
