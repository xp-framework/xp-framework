<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Test class for Marshaller / Unmarshaller tests.
   *
   * @see      xp://net.xp_framework.unittest.xml.UnmarshallerTest
   * @see      xp://net.xp_framework.unittest.xml.MarshallerTest
   * @purpose  Test class
   */
  class TextInputType extends Object {
    protected $id = '';
    protected $disabled = FALSE;

    /**
     * Cast to a bool
     *
     * @param   string string
     * @return  bool
     */
    public function asBool($string) {
      switch ($string) {
        case 'true': return TRUE;
        case 'false': return FALSE;
        default: throw new IllegalArgumentException('Unrecognized boolean value '.$value);
      }
    }

    /**
     * Cast to a string
     *
     * @param   bool bool
     * @return  string
     */
    public function toBool($bool) {
      return $bool ? 'true' : 'false';
    }

    /**
     * Set ID
     *
     * @param   string id
     */
    #[@xmlmapping(element= '@id')]
    public function setId($id) {
      $this->id= $id;
    }

    /**
     * Get ID
     *
     * @return  string id
     */
    #[@xmlfactory(element= '@id')]
    public function getId() {
      return $this->id;
    }

    /**
     * Set disabled
     *
     * @param   bool disabled
     */
    #[@xmlmapping(element= '@disabled', cast= 'asBool')]
    public function setDisabled($disabled) {
      $this->disabled= $disabled;
    }

    /**
     * Get disabled
     *
     * @return  bool disabled
     */
    #[@xmlfactory(element= '@disabled', cast= 'toBool')]
    public function getDisabled() {
      return $this->disabled;
    }
  }
?>
