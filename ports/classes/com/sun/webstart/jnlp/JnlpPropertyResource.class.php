<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('com.sun.webstart.jnlp.JnlpResource');

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
     * @access  public
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
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set Value
     *
     * @access  public
     * @param   string value
     */
    public function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  string
     */
    public function getValue() {
      return $this->value;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    public function getTagName() { 
      return 'property';
    }

    /**
     * Get attributes
     *
     * @access  public
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
