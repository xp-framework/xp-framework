<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Handler generator
 !
 ! $Id$
 !-->
<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:cus="http://www.schlund.de/pustefix/customize"
 xmlns:pfx="http://www.schlund.de/pustefix/core"
 xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:exsl="http://exslt.org/common"
>
  <xsl:output method="text" indent="no"/>
  <xsl:include href="common.inc.xsl"/>
  
  <xsl:variable name="wrapper">
    <xsl:variable name="basename" select="substring-after(/interface/handler/@class, '.handler.')"/>
    <xsl:value-of select="substring-before(/interface/handler/@class, '.handler.')"/>.wrapper.<xsl:value-of select="substring-before($basename, 'Handler')"/>Wrapper
  </xsl:variable>

  <!--
   ! Template for root node
   !
   !-->
  <xsl:template match="/">

    <!-- Class header -->
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id]]>&#36;<![CDATA[
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    ']]></xsl:text>
    <xsl:value-of select="normalize-space($wrapper)"/><xsl:text>'
  );

  /**
   * Handler. &lt;Add description&gt;
   *
   * @purpose  &lt;Add purpose&gt;
   */
  class </xsl:text><xsl:call-template name="classname">
    <xsl:with-param name="string" select="/interface/handler/@class"/>
    </xsl:call-template><xsl:text> extends Handler {

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
      $this->setWrapper(new </xsl:text><xsl:call-template name="classname">
        <xsl:with-param name="string" select="normalize-space($wrapper)"/>
      </xsl:call-template><xsl:text><![CDATA[());
    }
    
    /**
     * Retrieve identifier.
     *
     * @access  public
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  string
     */
    function identifierFor(&$request, &$context) {
    
      // TODO: Implement this method, if a somehow unique identifier is required for this
      //       handler. If not, remove the method.
      
      return $this->name;
    }
    
    /**
     * Setup handler.
     *
     * @access  public
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  bool
     */
    function setup(&$request, &$context) {
    
      // TODO: Add code that is required to initially setup the handler
      //       Set values with Handler::setFormValue() to make them accessible in the frontend.
      
      return TRUE;
    }
    
    /**
     * Handle submitted data.
     *
     * @access  public
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function handleSubmittedData(&$request, &$context) {
      
      // TODO: Add code that handles the submitted values. The values have already
      //       passed the Wrappers precheck/caster/postcheck routines.
      
      return TRUE;
    }
    
    /**
     * Finalize this handler
     *
     * @access  public
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.Context context
     */
    function finalize(&$request, &$response, &$context) {

      // TODO: Add code that is executed after success and on every reload of the handler.
      //       Many handlers don't need this, so remove the complete function.
    }
  }
?>]]></xsl:text>
  </xsl:template>
</xsl:stylesheet>
