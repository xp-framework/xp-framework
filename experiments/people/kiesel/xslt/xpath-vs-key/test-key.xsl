<?xml version="1.0" encoding="iso-8859-1"?>

<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func hlt"
>

<xsl:output
  method="text"
  encoding="iso-8859-1"
  indent="no"
/>

  <!-- Create an index -->
  <xsl:key name="gettext" match="text" use="@for"/>

  <!-- Load texts -->
  <xsl:variable name="texts" select="document('texts-attr.xml')/texts"/>

  <!-- Template for matching texts -->
  <xsl:template match="texts">
    <xsl:param name="current-node"/>
    
    <xsl:value-of select="key('gettext', $current-node)"/>
  </xsl:template>

  <!-- Function for gettext -->
  <func:function name="hlt:gettext">
    <xsl:param name="text"/>
    
    <func:result><xsl:apply-templates select="$texts"><xsl:with-param name="current-node" select="$text"/></xsl:apply-templates></func:result>
  </func:function>

  <!--
   !
   ! Main begins here
   !
   !
   !-->

  <!-- Helper to apply templates -->
  <xsl:template match="apply-node">
    <!-- Result: <xsl:apply-templates select="$texts"><xsl:with-param name="current-node" select="'emaillist#unread'"/></xsl:apply-templates> -->
    <xsl:value-of select="hlt:gettext('emaillist#unread')"/>
  </xsl:template>

  <xsl:template match="/">
    <xsl:for-each select="document/apply-node">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
