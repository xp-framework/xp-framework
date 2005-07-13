<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:xpdoc="http://xp-framework.net/TR/apidoc/"
>
  <xsl:output method="text"/>

  <!--
   ! Template to create apidoc 
   ! for normal methods
   !
   !-->
  <xsl:template name="createmethod">
    <xsl:param name="method"/>
    <xsl:text><![CDATA[
    /**
     *
     * @access  public]]>&#10;</xsl:text>
      <xsl:for-each select="$method/parameters/parameter">
        <xsl:text>     * @param   </xsl:text>
        <xsl:value-of select="@type"/>
        <xsl:text> </xsl:text>
        <xsl:value-of select="@name"/>
        <xsl:text>&#10;</xsl:text>
      </xsl:for-each>
      <xsl:if test="return/@type != 'void'">
        <xsl:text>     * @return  </xsl:text>
        <xsl:value-of select="return/@type"/>
        <xsl:text>&#10;</xsl:text>
      </xsl:if>
      <xsl:text><![CDATA[     */
    function ]]></xsl:text>
      <xsl:value-of select="$method/@name"/><xsl:text>(</xsl:text>
      <xsl:for-each select="parameters/parameter">
        <xsl:value-of select="concat('$param', position())"/>
        <xsl:if test="position() &lt; last()">, </xsl:if>
      </xsl:for-each>
      <xsl:text>) { }</xsl:text>
  </xsl:template>

  <!--
   ! Template to create apidoc and annotations
   ! for overloaded methods
   !
   !-->
  <xsl:template name="overloadmethod">
    <xsl:param name="name"/>
    <xsl:text><![CDATA[
    /**
     *
     * @access  public
     * @param   mixed* args
     */
    #[@overloaded(variants= array(
]]></xsl:text>
    <xsl:for-each select="/interface/method[@name= $name]">
      <xsl:text><![CDATA[    #  array(]]></xsl:text>
      <xsl:for-each select="parameters/parameter">
        <xsl:text><![CDATA[']]></xsl:text>
        <xsl:value-of select="@type"/>
        <xsl:text><![CDATA[']]></xsl:text>
        <xsl:if test="not(position() = last())">
          <xsl:text>,</xsl:text>
        </xsl:if>
      </xsl:for-each>
      <xsl:text>)</xsl:text>
      <xsl:if test="not(position() = last())">
        <xsl:text>,</xsl:text>
      </xsl:if>
      <xsl:text>&#10;</xsl:text>
    </xsl:for-each>
    <xsl:text><![CDATA[    #))]
    function ]]></xsl:text>
    <xsl:value-of select="$name"/>
    <xsl:text><![CDATA[() { }]]></xsl:text>
  </xsl:template>

  <!--
   ! Template to match on root node
   !
   !-->
  <xsl:template match="/">
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * ]]></xsl:text><xsl:value-of select="/interface/@display-name"/><xsl:text><![CDATA[
   *
   * @purpose  Remote interface
   */
  class ]]></xsl:text><xsl:value-of select="/interface/@name"/><xsl:text><![CDATA[ extends Interface {
    ]]></xsl:text>

    <xsl:variable name="interface" select="/interface"/>
    <xsl:for-each select="/interface/method">
      <xsl:variable name="name" select="@name"/>
      <xsl:variable name="pos" select="position()"/>
      <xsl:if test="$interface/method[position() = $pos - 1]/@name != $name">

        <xsl:choose>
          <xsl:when test="count($interface/method[@name = $name]) &gt; 1">
            <xsl:call-template name="overloadmethod">
              <xsl:with-param name="name" select="@name"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:call-template name="createmethod">
              <xsl:with-param name="method" select="."/>
            </xsl:call-template> 
          </xsl:otherwise>
        </xsl:choose>
        <xsl:text>&#10;&#10;</xsl:text>
      </xsl:if>
    </xsl:for-each>
    <xsl:text><![CDATA[
  }
?>
]]></xsl:text>
  </xsl:template>
</xsl:stylesheet>
