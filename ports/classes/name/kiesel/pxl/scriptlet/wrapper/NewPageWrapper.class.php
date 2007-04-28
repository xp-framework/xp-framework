<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Wrapper',
    'scriptlet.xml.workflow.casters.ToDate',
    'scriptlet.xml.workflow.casters.ToFileData',
    'scriptlet.xml.workflow.checkers.FileUploadPrechecker'    
  );

  /**
   * Wrapper for NewPageHandler
   * Handler
   * 
   * @see      xp://name.kiesel.pxl.scriptlet.handler.NewPageHandler
   * @purpose  Wrapper
   */
  class NewPageWrapper extends Wrapper {

    /**
     * Constructor
     *
     */  
    function __construct() {
      $this->registerParamInfo(
        'name',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'description',
        OCCURRENCE_UNDEFINED,
        NULL,
        NULL,
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'file',
        OCCURRENCE_UNDEFINED,
        NULL,
        array('scriptlet.xml.workflow.casters.ToFileData'),
        array('scriptlet.xml.workflow.checkers.FileUploadPrechecker'),
        NULL
      );
      $this->registerParamInfo(
        'published',
        OCCURRENCE_OPTIONAL,
        NULL,
        array('scriptlet.xml.workflow.casters.ToDate'),
        NULL,
        NULL
      );
      $this->registerParamInfo(
        'tags',
        OCCURRENCE_OPTIONAL,
        NULL,
        NULL,
        NULL,
        NULL
      );
    }

    /**
     * Returns the value of the parameter name
     *
     * @return  string
     */
    function getName() {
      return $this->getValue('name');
    }

    /**
     * Returns the value of the parameter description
     *
     * @return  string
     */
    function getDescription() {
      return $this->getValue('description');
    }

    /**
     * Returns the value of the parameter file
     *
     * @return  string
     */
    function getFile() {
      return $this->getValue('file');
    }

    /**
     * Returns the value of the parameter published
     *
     * @return  string
     */
    function getPublished() {
      return $this->getValue('published');
    }

    /**
     * Returns the value of the parameter tags
     *
     * @return  string
     */
    function getTags() {
      return $this->getValue('tags');
    }

  }
?>
