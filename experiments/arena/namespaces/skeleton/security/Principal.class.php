<?php
/* This class is part of the XP framework
 *
 * $Id: Principal.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace security;

  /**
   * This class represents the abstract notion of a principal, which can be used 
   * to represent any entity, such as an individual, a corporation, and a login id. 
   *
   * @see      xp://security.cert.X509Certificate
   * @purpose  Principal
   */
  class Principal extends lang::Object {
    public
      $dn= array();
      
    /**
     * Constructor
     *
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
     * @return  string
     */
    public function getCountryName() {
      return $this->dn['C'];
    }
    
    /**
     * Get state or province name
     *
     * @return  string
     */
    public function getStateOrProvinceName() {
      return $this->dn['ST'];
    }
    
    /**
     * Get locality name
     *
     * @return  string
     */
    public function getLocalityName() {
      return $this->dn['L'];
    }
    
    /**
     * Get organization name
     *
     * @return  string
     */
    public function getOrganizationName() {
      return $this->dn['O'];
    }
    
    /**
     * Get organizational unit name
     *
     * @return  string
     */
    public function getOrganizationalUnitName() {
      return $this->dn['OU'];
    }
    
    /**
     * Get common name
     *
     * @return  string
     */
    public function getCommonName() {
      return $this->dn['CN'];
    }
    
    /**
     * Get email address
     *
     * @return  string
     */
    public function getEmailAddress() {
      return $this->dn['EMAIL'];
    }
  }
?>
