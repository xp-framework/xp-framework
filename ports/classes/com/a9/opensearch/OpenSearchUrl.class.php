<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents an open search URL
   *
   * @see      xp://com.a9.opensearch.OpenSearchDescription
   * @purpose  purpose
   */
  #[@xmlns(s= 'http://a9.com/-/spec/opensearch/1.1/')]
  class OpenSearchUrl extends Object {
    protected
      $type       = '',
      $template   = '';

    /**
     * Set type
     *
     * @param   string type
     */
    #[@xmlmapping(element= '@type')]
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get type
     *
     * @return  string
     */
    #[@xmlfactory(element= '@type')]
    public function getType() {
      return $this->type;
    }

    /**
     * Set template
     *
     * @param   string template
     */
    #[@xmlmapping(element= '@template')]
    public function setTemplate($template) {
      $this->template= $template;
    }

    /**
     * Get template
     *
     * @return  string
     */
    #[@xmlfactory(element= '@template')]
    public function getTemplate() {
      return $this->template;
    }
  }
?>
