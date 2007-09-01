<?php
/* This class is part of the XP framework
 *
 * $Id: OpenSearchUrl.class.php 9788 2007-03-28 18:07:21Z friebe $ 
 */

  namespace com::a9::opensearch;

  /**
   * Represents an open search URL
   *
   * @see      xp://com.a9.opensearch.OpenSearchDescription
   * @purpose  purpose
   */
  #[@xmlns(s= 'http://a9.com/-/spec/opensearch/1.1/')]
  class OpenSearchUrl extends lang::Object {
    protected
      $type       = '',
      $template   = '';

    /**
     * Constructor
     *
     * @param   string type default '' content type
     * @param   string template default ''
     */
    public function __construct($type= '', $template= '') {
      $this->type= $type;
      $this->template= $template;
    }

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
