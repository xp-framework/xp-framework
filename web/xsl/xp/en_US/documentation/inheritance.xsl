<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: documentation.xsl 4505 2005-01-07 11:04:54Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../../layout.xsl"/>
  
  <xsl:variable name="tree" select="document('../../../../build/inheritance.xml')/inheritance"/>

  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>Inheritance tree</h1>

    <ul>
    <xsl:for-each select="$tree/class">
      <xsl:sort select="@name"/>
      <xsl:call-template name="show-inheritance">
        <xsl:with-param name="tree" select="."/>
        <xsl:with-param name="class" select="@name"/>
      </xsl:call-template>
    </xsl:for-each>
    </ul>
  </xsl:template>

  <xsl:template name="show-inheritance">
    <xsl:param name="tree"/>
    <xsl:param name="class"/>
    <xsl:param name="depth" select="0"/>
    
    <li>
      <a href="{func:link(concat('lookup?', $class))}"><xsl:value-of select="$class"/></a>
    </li>
    
    <xsl:if test="count($tree/class) &gt; 0">
      <ul>
        <xsl:for-each select="$tree/class">
          <xsl:sort select="@name"/>
          <xsl:call-template name="show-inheritance">
            <xsl:with-param name="tree" select="."/>
            <xsl:with-param name="class" select="@name"/>
            <xsl:with-param name="depth" select="$depth + 1"/>
          </xsl:call-template>
        </xsl:for-each>
      </ul>
    </xsl:if>
  </xsl:template>
</xsl:stylesheet>
