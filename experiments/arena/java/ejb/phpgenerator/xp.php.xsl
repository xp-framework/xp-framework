<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="text"/>
  
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
     * ]]></xsl:text><xsl:value-of select="comment"/><xsl:text><![CDATA[
     *
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
      <xsl:value-of select="@name"/>
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
