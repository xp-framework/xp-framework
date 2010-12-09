<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'com.google.search.custom.types';

  /**
   * Keymatches entry
   *
   * @see   xp://com.google.search.custom.types.Response#getKeyMatches
   */
  class com·google·search·custom·types·KeyMatch extends Object {
    protected $url= '';
    protected $text= '';
    
    /**
     * Sets URL
     *
     * @param   string url
     */
    #[@xmlmapping(element= 'GL')]
    public function setUrl($url) {
      $this->url= $url;
    }

    /**
     * Gets URL
     *
     * @return  string url
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Sets text
     *
     * @param   string text
     */
    #[@xmlmapping(element= 'GD')]
    public function setText($text) {
      $this->text= $text;
    }

    /**
     * Gets text
     *
     * @return  string text
     */
    public function getText() {
      return $this->text;
    }

    /**
     * Creates a string representation of this result set
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'(url= '.$this->url.', "'.$this->text.'")';
    }
  }
?>
