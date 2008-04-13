<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
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
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links
   !-->
  <xsl:template match="link">
    <a href="{@href}"><xsl:value-of select="@href"/></a>
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
 
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <xsl:apply-templates select="/formresult/documentation/*"/>
        </td>
        <td id="context">
          Table of contents
          <ul style="margin-left: 6px">
            <xsl:for-each select="/formresult/documentation/h2">
              <li><a href="#{string(.)}"><xsl:value-of select="."/></a></li>
            </xsl:for-each>
          </ul>
        </td>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template name="html-title">
    <xsl:value-of select="/formresult/documentation/h1"/> - 
    XP Framework Documentation
  </xsl:template>
  
  <xsl:template name="context">
  </xsl:template>
</xsl:stylesheet>
