<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Documentation commons
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="layout.inc.xsl"/>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>

  <!-- 
   ! Transform <code> ... </code> to <pre class="code"> ... </pre>
   ! because of IE's lacking support for white-space: pre
   !-->
  <xsl:template match="code">
    <pre class="code">
      <xsl:apply-templates/>
    </pre>
  </xsl:template>

  <!--
   ! Turn "h2"-headlines into scroll links
   !-->
  <xsl:template match="h2">
    <a name="#{string(.)}"><h2><xsl:value-of select="."/></h2></a>
  </xsl:template>

  <!--
   ! Links to documentation
   !-->
  <xsl:template match="link[@rel= 'doc']">
    <a href="/xml/doc?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'package']">
    <a href="/xml/api/package?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'class']">
    <a href="/xml/api/class?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links without caption
   !-->
  <xsl:template match="link[string(.) = '']">
    <a href="{@rel}://{@href}"><xsl:value-of select="concat(@rel, '://', @href)"/></a>
  </xsl:template>

  <!--
   ! Links
   !-->
  <xsl:template match="link">
    <a href="{@rel}://{@href}"><xsl:value-of select="."/></a>
  </xsl:template>


  <!--
   ! Summary
   !-->
  <xsl:template match="summary">
    <fieldset class="summary">
      <xsl:apply-templates/>
    </fieldset>
  </xsl:template>

  <xsl:template match="documentation">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
</xsl:stylesheet>
