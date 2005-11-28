<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('JNLP_DESCR_DEFAULT',    '');
  define('JNLP_DESCR_SHORT',      'short');
  define('JNLP_DESCR_TOOLTIP',    'tooltip');

  /**
   * Represents JNLP information
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Wrapper class
   */
  class JnlpInformation extends Object {
    var
      $title          = '',
      $vendor         = '',
      $homepage       = '',
      $icon           = '',
      $description    = array(),
      $offlineAllowed = FALSE;

    /**
     * Set OfflineAllowed
     *
     * @access  public
     * @param   bool offlineAllowed
     */
    function setOfflineAllowed($offlineAllowed) {
      $this->offlineAllowed= $offlineAllowed;
    }

    /**
     * Get OfflineAllowed
     *
     * @access  public
     * @return  bool
     */
    function getOfflineAllowed() {
      return $this->offlineAllowed;
    }

    /**
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set Vendor
     *
     * @access  public
     * @param   string vendor
     */
    function setVendor($vendor) {
      $this->vendor= $vendor;
    }

    /**
     * Get Vendor
     *
     * @access  public
     * @return  string
     */
    function getVendor() {
      return $this->vendor;
    }

    /**
     * Set Homepage
     *
     * @access  public
     * @param   string homepage
     */
    function setHomepage($homepage) {
      $this->homepage= $homepage;
    }

    /**
     * Get Homepage
     *
     * @access  public
     * @return  string
     */
    function getHomepage() {
      return $this->homepage;
    }

    /**
     * Set Icon
     *
     * @access  public
     * @param   string icon
     */
    function setIcon($icon) {
      $this->icon= $icon;
    }

    /**
     * Get Icon
     *
     * @access  public
     * @return  string
     */
    function getIcon() {
      return $this->icon;
    }

    /**
     * Set default description
     *
     * @access  public
     * @param   string description
     * @param   string type default JNLP_DESCR_DEFAULT one of the JNLP_DESCR_* constants
     */
    function setDescription($description, $type= JNLP_DESCR_DEFAULT) {
      $this->description[$type]= $description;
    }

    /**
     * Get default description
     *
     * @access  public
     * @param   string type default JNLP_DESCR_DEFAULT one of the JNLP_DESCR_* constants
     * @return  string
     */
    function getDescription($type= JNLP_DESCR_DEFAULT) {
      return $this->description[$type];
    }

  }
?>
