<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This class represents the abstract notion of a principal, which can be used 
   * to represent any entity, such as an individual, a corporation, and a login id. 
   *
   * @see      xp://security.cert.X509Certificate
   * @purpose  Principal
   */
  class Principal extends Object {
    public
      $dn= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array dn
     */
    public function __construct($dn) {
      $this->dn= array_change_key_case($dn, CASE_UPPER);
    }
  
    /**
     * Get name
     *
     * Example:
     * <pre>
     * /C=DE/ST=Germany/L=Karlsruhe/O=Foo/OU=Bar/CN=Foo, Baz Bar/EMAIL=mail@example.com
     * </pre>
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      $name= '';
      foreach ($this->dn as $k => $v) {
        $name.= '/'.$k.'='.$v;
      }
      return $name;
    }
    
    /**
     * Get country name
     *
     * @access  public
     * @return  string
     */
    public function getCountryName() {
      return $this->dn['C'];
    }
    
    /**
     * Get state or province name
     *
     * @access  public
     * @return  string
     */
    public function getStateOrProvinceName() {
      return $this->dn['ST'];
    }
    
    /**
     * Get locality name
     *
     * @access  public
     * @return  string
     */
    public function getLocalityName() {
      return $this->dn['L'];
    }
    
    /**
     * Get organization name
     *
     * @access  public
     * @return  string
     */
    public function getOrganizationName() {
      return $this->dn['O'];
    }
    
    /**
     * Get organizational unit name
     *
     * @access  public
     * @return  string
     */
    public function getOrganizationalUnitName() {
      return $this->dn['OU'];
    }
    
    /**
     * Get common name
     *
     * @access  public
     * @return  string
     */
    public function getCommonName() {
      return $this->dn['CN'];
    }
    
    /**
     * Get email address
     *
     * @access  public
     * @return  string
     */
    public function getEmailAddress() {
      return $this->dn['EMAIL'];
    }
  }
?>
