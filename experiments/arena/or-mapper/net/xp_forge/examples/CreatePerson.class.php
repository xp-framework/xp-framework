<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_forge.examples.AbstractExampleCommand');

  /**
   * Creates a person
   *
   * @purpose  Example
   */
  class CreatePerson extends net·xp_forge·examples·AbstractExampleCommand {
    protected
      $person= NULL;
    
    /**
     * Constructor. Creates person member
     *
     */
    public function __construct() {
      $this->person= new Person();
      $this->person->setLastchange(Date::now());
      $this->person->setChangedby($this->getClassName());
      $this->person->setBz_id(10000);
    }
    
    /**
     * Set firstname
     *
     * @param   string firstname
     */
    #[@arg]
    public function setFirstname($firstname) {
      $this->person->setFirstname($firstname);
    }

    /**
     * Set lastname
     *
     * @param   string lastname
     */
    #[@arg]
    public function setLastname($lastname) {
      $this->person->setLastname($lastname);
    }

    /**
     * Set email
     *
     * @param   string email
     */
    #[@arg]
    public function setEmail($email) {
      $this->person->setemail($email);
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->out->writeLine('===> Creating person ', $this->person);
      $id= $this->person->save();
      $this->out->writeLine('---> Done, ID= ', $id);
    }
  }
?>
