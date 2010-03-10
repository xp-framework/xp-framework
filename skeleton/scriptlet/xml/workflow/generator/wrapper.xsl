<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Wrapper generator
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
 xmlns:set="http://exslt.org/sets"
>
  <xsl:output method="text" indent="no"/>
  <xsl:include href="common.inc.xsl"/>
  
  <!--
   ! Template that creates a bitfield from occurence values
   !
   ! @param  string value
   !-->  
  <xsl:template name="occurrence">
    <xsl:param name="value"/>
    <xsl:param name="names">
      <name for="optional">Wrapper::OCCURRENCE_OPTIONAL</name>
      <name for="multiple">Wrapper::OCCURRENCE_MULTIPLE</name>
      <name for="passbehind">Wrapper::OCCURRENCE_PASSBEHIND</name>
    </xsl:param>

    <xsl:choose>
      <xsl:when test="contains($value, ',')">
        <xsl:value-of select="exsl:node-set($names)/name[@for = substring-before($value, ',')]"/>
        <xsl:text> | </xsl:text>
        <xsl:call-template name="occurrence">
          <xsl:with-param name="value" select="substring-after($value, ',')"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="exsl:node-set($names)/name[@for = $value]"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template> 

  <!--
   ! Template for creating API doc comments
   !
   ! @type   named
   ! @param  string comment
   ! @param  string indent default '  '
   !-->
  <xsl:template name="comment">
    <xsl:param name="content"/>
    <xsl:param name="indent" select="'  '"/>
 
    <xsl:value-of select="concat($indent, ' * ')"/>
    <xsl:variable name="remaining" select="substring-after($content, '&#xA;')"/>
    <xsl:value-of select="concat(normalize-space(
      substring($content, 
      1, 
      string-length($content) - string-length($remaining))
    ), '&#10;')"/>
    <xsl:if test="$remaining != ''">  
      <xsl:call-template name="comment">
        <xsl:with-param name="content" select="$remaining"/>
        <xsl:with-param name="indent" select="$indent"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

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
    'scriptlet.xml.workflow.Wrapper']]></xsl:text>

    <!-- Insert the unique list of all used classes -->
    <xsl:for-each select="set:distinct(//postcheck/@class | //precheck/@class | //caster/@class)">
      <xsl:sort select="."/>
 
      <xsl:text>,&#10;</xsl:text>        
      <xsl:text>    '</xsl:text>
      <xsl:value-of select="."/>
      <xsl:text>'</xsl:text>
    </xsl:for-each>
    <xsl:text><![CDATA[    
  );

  /**
   * Wrapper for ]]></xsl:text>
    <xsl:call-template name="classname">
      <xsl:with-param name="string" select="/interface/handler/@class"/>
    </xsl:call-template>
    <xsl:text>&#10;</xsl:text>
    <xsl:call-template name="comment">
      <xsl:with-param name="content" select="/interface/comment()"/>
    </xsl:call-template>
    <xsl:text><![CDATA[   * 
   * @see      xp://]]></xsl:text><xsl:value-of select="/interface/handler/@class"/><xsl:text><![CDATA[
   * @purpose  Wrapper
   */
  class ]]></xsl:text>
    <xsl:call-template name="classname">
      <xsl:with-param name="string" select="/interface/handler/@class"/>
      <xsl:with-param name="trim" select="'Handler'"/>
    </xsl:call-template>
    <xsl:text><![CDATA[Wrapper extends Wrapper {
]]></xsl:text>

    <!-- Apply interface -->
    <xsl:apply-templates/>
    
    <!-- Class footer -->
    <xsl:text><![CDATA[
  }
?>
]]></xsl:text>
  </xsl:template>

  <!--
   ! Template for default node
   !
   ! Example (literal use):
   ! <code>
   !   <default>Date::now()</default>
   ! </code>
   !
   ! Example (constant value):
   ! <code>
   !   <default><value xsi:type="xsd:string">Binford</value></default>
   ! </code>
   !
   !-->  
  <xsl:template match="default">
    <xsl:apply-templates select="value|text()"/>
  </xsl:template>

  <!--
   ! Template for cparams / values with type "xsd:boolean"
   !
   !-->
  <xsl:template match="cparam[@xsi:type= 'xsd:boolean']|value[@xsi:type= 'xsd:boolean']">
    <xsl:choose>
      <xsl:when test=". = 'true'">TRUE</xsl:when>
      <xsl:otherwise>FALSE</xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <!--
   ! Template for cparams / values with type "xsd:string"
   !
   !-->
  <xsl:template match="cparam[@xsi:type= 'xsd:string']|value[@xsi:type= 'xsd:string']">
    <xsl:text>'</xsl:text>
    <xsl:value-of select="."/>
    <xsl:text>'</xsl:text>    
  </xsl:template>

  <!--
   ! Template for cparams with type "xsd:array"
   !
   !-->
  <xsl:template match="cparam[@xsi:type= 'xsd:array']|value[@xsi:type= 'xsd:array']">
    <xsl:text>array(</xsl:text>
    <xsl:for-each select="value">
      <xsl:apply-templates select="."/>
      <xsl:if test="position() != last()">, </xsl:if>
    </xsl:for-each>
    <xsl:text>)</xsl:text>    
  </xsl:template>

  <!--
   ! Generic template for cparams
   !
   !-->
  <xsl:template match="cparam|value">
    <xsl:value-of select="."/>
  </xsl:template>

  <!--
   ! Template for caster and checkers
   !
   !-->
  <xsl:template match="caster|precheck|postcheck">
    <xsl:text>array('</xsl:text>
    <xsl:value-of select="@class"/>
    <xsl:text>'</xsl:text>
    <xsl:for-each select="cparam">
      <xsl:text>, </xsl:text>
      <xsl:apply-templates select="."/>
    </xsl:for-each>
    <xsl:text>)</xsl:text>
  </xsl:template>

  <!--
   ! Template for interface node (with version "1.0")
   !
   !-->  
  <xsl:template match="interface[@version= '1.0']">
    <xsl:text><![CDATA[
    /**
     * Constructor
     *
     */  
    public function __construct() {]]></xsl:text>      
    
    <!-- Create registerParamInfo() calls -->
    <xsl:for-each select="param">
      <xsl:text><![CDATA[
      $this->registerParamInfo(
        ']]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[',
        ]]></xsl:text>
        <xsl:choose>
          <xsl:when test="not(@occurrence)">Wrapper::OCCURRENCE_UNDEFINED</xsl:when>
          <xsl:otherwise>
            <xsl:call-template name="occurrence">
              <xsl:with-param name="value" select="@occurrence"/>
            </xsl:call-template>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:text><![CDATA[,
        ]]></xsl:text>
        <xsl:if test="not(default)">NULL</xsl:if>
        <xsl:apply-templates select="default"/>
        <xsl:text><![CDATA[,
        ]]></xsl:text>
        <xsl:if test="not(caster)">NULL</xsl:if>
        <xsl:apply-templates select="caster"/>
        <xsl:text><![CDATA[,
        ]]></xsl:text>
        <xsl:if test="not(precheck)">NULL</xsl:if>
        <xsl:apply-templates select="precheck"/>
        <xsl:text><![CDATA[,
        ]]></xsl:text>
        <xsl:if test="not(postcheck)">NULL</xsl:if>
        <xsl:apply-templates select="postcheck"/>
        <xsl:text><![CDATA[
      );]]></xsl:text>
    </xsl:for-each>
    <xsl:text><![CDATA[
    }
]]></xsl:text>
    
    <!-- Create getters and setters -->
    <xsl:for-each select="param">
      <xsl:text><![CDATA[
    /**
     * Returns the value of the parameter ]]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[
     *
     * @return  ]]></xsl:text>
      <xsl:if test="contains(@type, '.')">&amp;</xsl:if>
      <xsl:value-of select="@type"/>
      <xsl:if test="contains(@occurrence, 'multiple')">[]</xsl:if>
    <xsl:text><![CDATA[
     */
    public function ]]></xsl:text>
    <xsl:if test="contains(@type, '.')">&amp;</xsl:if>
    <xsl:text>get</xsl:text>
    <xsl:call-template name="name">
      <xsl:with-param name="string" select="@name"/>
    </xsl:call-template>
    <xsl:text><![CDATA[() {
      return $this->getValue(']]></xsl:text><xsl:value-of select="@name"/><xsl:text><![CDATA[');
    }
]]></xsl:text>
    </xsl:for-each>
  </xsl:template>
  
  <!--
   ! Template for interface node. This is a "fall-through" template, meaning
   ! it is called only when no other template (with more specific rules for
   ! the interface node) has matched before.
   !
   ! It will raise an XSL message with the text:
   !
   !   Interface version "XX" not supported!
   !
   ! and terminate processing
   !
   ! @see  wrapper.xsl/templates/match/interface[@version= '1.0']
   !-->  
  <xsl:template match="interface">
    <xsl:message terminate="yes">
      <xsl:text>Interface version "</xsl:text>
      <xsl:value-of select="@version"/> 
      <xsl:text>" not supported!</xsl:text>
    </xsl:message>
  </xsl:template>
  
</xsl:stylesheet>
