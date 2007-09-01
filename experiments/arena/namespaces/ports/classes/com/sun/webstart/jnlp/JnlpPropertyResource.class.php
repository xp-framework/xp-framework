<?php
/* This class is part of the XP framework
 *
 * $Id: JnlpPropertyResource.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::sun::webstart::jnlp;

  ::uses('com.sun.webstart.jnlp.JnlpResource');

  /**
   * JNLP resource that points to a system propetry
   *
   * XML representation
   * <pre>
   *   <property name="org.omg.CORBA.ORBClass" value="com.inprise.vbroker.orb.ORB"/>
   * </pre>
   *
   * @see      xp://com.sun.webstart.JnlpResource
   * @purpose  JNLP resource
   */
  class JnlpPropertyResource extends JnlpResource {
    public 
      $name   = '',
      $value  = '';

    /**
     * Constructor
     *
     * @param   string name
     * @param   string value
     */
    public function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Value
     *
     * @param   string value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @return  string
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getTagName() { 
      return 'property';
    }

    /**
     * Get attributes
     *
     * @return  array
     */
    public function getTagAttributes() { 
      return array(
        'name'  => $this->name,
        'value' => $this->value
      );
    }
  }
?>
