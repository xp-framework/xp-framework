<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:xpdoc="http://xp-framework.net/TR/apidoc/"
>
  <xsl:output method="text"/>
  
  <!--
   ! Template for creating API doc comments
   !
   ! @type   named
   ! @param  string name
   ! @param  string indent default '  '
   !-->
  <xsl:template name="xpdoc:comment">
    <xsl:param name="string"/>
    <xsl:param name="indent" select="'  '"/>
 
    <xsl:value-of select="concat($indent, ' * ')"/>
   
    <xsl:choose>
      <xsl:when test="normalize-space($string) = ''">
        <xsl:text>(Insert documentation here)&#10;</xsl:text>
      </xsl:when>
      <xsl:otherwise>
        <xsl:variable name="remaining" select="substring-after($string, '&#xA;')"/>
        <xsl:value-of select="concat(
          normalize-space(substring($string, 1, string-length($string) - string-length($remaining))),
          '&#10;'
        )"/>
        <xsl:if test="$remaining != ''">  
          <xsl:call-template name="xpdoc:comment">
            <xsl:with-param name="string" select="$remaining"/>
            <xsl:with-param name="indent" select="$indent"/>
          </xsl:call-template>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
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
  class ]]></xsl:text><xsl:value-of select="/interface/@name"/><xsl:text><![CDATA[Remote extends Interface {
    ]]></xsl:text>
    <xsl:for-each select="/interface/method"><xsl:text><![CDATA[
    /**
]]></xsl:text>
      <xsl:call-template name="xpdoc:comment">
        <xsl:with-param name="string" select="comment"/>
        <xsl:with-param name="indent" select="'    '"/>
      </xsl:call-template>
      <xsl:text><![CDATA[     *
     * @access  public]]>&#10;</xsl:text>
      <xsl:for-each select="parameters/parameter">
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
      <xsl:variable name="name" select="@name"/>
      <xsl:choose>
        <xsl:when test="count(/interface/method[@name = $name]) &gt; 1 and . != /interface/method[@name = $name][1]">
          <xsl:message>*** Method <xsl:value-of select="$name"/>() already exists! ***</xsl:message>
          <xsl:value-of select="concat($name, position())"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$name"/>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:text>(</xsl:text>
      <xsl:for-each select="parameters/parameter">
        <xsl:value-of select="concat('$', @name)"/>
        <xsl:if test="position() &lt; last()">, </xsl:if>
      </xsl:for-each>
      <xsl:text>) { }&#10;</xsl:text>
    </xsl:for-each><xsl:text><![CDATA[
  }
?>
]]></xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
