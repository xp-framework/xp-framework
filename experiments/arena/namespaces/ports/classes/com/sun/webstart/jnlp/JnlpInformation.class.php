<?php
/* This class is part of the XP framework
 *
 * $Id: JnlpInformation.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::sun::webstart::jnlp;

  define('JNLP_DESCR_DEFAULT',    '');
  define('JNLP_DESCR_SHORT',      'short');
  define('JNLP_DESCR_TOOLTIP',    'tooltip');

  /**
   * Represents JNLP information
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Wrapper class
   */
  class JnlpInformation extends lang::Object {
    public
      $title          = '',
      $vendor         = '',
      $homepage       = '',
      $icon           = '',
      $description    = array(),
      $offlineAllowed = FALSE;

    /**
     * Set OfflineAllowed
     *
     * @param   bool offlineAllowed
     */
    public function setOfflineAllowed($offlineAllowed) {
      $this->offlineAllowed= $offlineAllowed;
    }

    /**
     * Get OfflineAllowed
     *
     * @return  bool
     */
    public function getOfflineAllowed() {
      return $this->offlineAllowed;
    }

    /**
     * Set Title
     *
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    /**
     * Set Vendor
     *
     * @param   string vendor
     */
    public function setVendor($vendor) {
      $this->vendor= $vendor;
    }

    /**
     * Get Vendor
     *
     * @return  string
     */
    public function getVendor() {
      return $this->vendor;
    }

    /**
     * Set Homepage
     *
     * @param   string homepage
     */
    public function setHomepage($homepage) {
      $this->homepage= $homepage;
    }

    /**
     * Get Homepage
     *
     * @return  string
     */
    public function getHomepage() {
      return $this->homepage;
    }

    /**
     * Set Icon
     *
     * @param   string icon
     */
    public function setIcon($icon) {
      $this->icon= $icon;
    }

    /**
     * Get Icon
     *
     * @return  string
     */
    public function getIcon() {
      return $this->icon;
    }

    /**
     * Set default description
     *
     * @param   string description
     * @param   string type default JNLP_DESCR_DEFAULT one of the JNLP_DESCR_* constants
     */
    public function setDescription($description, $type= JNLP_DESCR_DEFAULT) {
      $this->description[$type]= $description;
    }

    /**
     * Get default description
     *
     * @param   string type default JNLP_DESCR_DEFAULT one of the JNLP_DESCR_* constants
     * @return  string
     */
    public function getDescription($type= JNLP_DESCR_DEFAULT) {
      return $this->description[$type];
    }

  }
?>
