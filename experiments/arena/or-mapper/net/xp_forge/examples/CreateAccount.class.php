<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_forge.examples.AbstractExampleCommand');

  /**
   * Creates an account
   *
   * @purpose  Example
   */
  class CreateAccount extends net·xp_forge·examples·AbstractExampleCommand {
    protected
      $account= NULL;
    
    /**
     * Constructor. Creates Account member
     *
     */
    public function __construct() {
      $this->account= new Account();
      $this->account->setLastchange(Date::now());
      $this->account->setChangedby($this->getClassName());
      $this->account->setBz_id(10000);
    }

    /**
     * Set person-id for whom this account is
     *
     * @param   int personId
     */
    #[@arg(name= 'id')]
    public function setPersonId($personId) {
      $this->account->setPerson_id($personId);
    }
    
    /**
     * Set username
     *
     * @param   string username
     */
    #[@arg]
    public function setUsername($username) {
      $this->account->setUsername($username);
    }

    /**
     * Set password
     *
     * @param   string password
     */
    #[@arg]
    public function setPassword($password) {
      $this->account->setPassword($password);
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      $this->out->writeLine('===> Creating account ', $this->account);
      $id= $this->account->save();
      $this->out->writeLine('---> Done, ID= ', $id);
    }
  }
?>
