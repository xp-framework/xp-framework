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

  <xsl:variable name="breadcrumb">
    <title for="cs">Coding standards</title>
    <title for="source">Sourcecode</title>
    <title for="mailinglists">Mailing lists</title>
  </xsl:variable>

  <xsl:template name="content">
    <xsl:variable name="base" select="/formresult/documentation/@base"/>

    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
            <a href="{xp:link(concat('static?', $base))}">
              <xsl:value-of select="exsl:node-set($breadcrumb)/title[@for= $base]"/>
            </a>
            <xsl:if test="/formresult/documentation/@topic"> &#xbb;
              <a href="{xp:link(concat('static?', $base, '/', /formresult/documentation/@topic))}">
                <xsl:value-of select="/formresult/documentation/h1"/>
              </a>
            </xsl:if>
          </div>

          <xsl:apply-templates select="/formresult/documentation/*"/>
        </td>
        <td id="context">
          <h3>Table of contents</h3>
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
    <xsl:value-of select="/formresult/documentation/h1"/> - XP Framework Developer Zone
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
    <a href="http://docs.xp-framework.net/xml/doc?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'package']">
    <a href="http://docs.xp-framework.net/xml/api/package?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'class']">
    <a href="http://docs.xp-framework.net/xml/api/class?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to an XP RFC
   !-->
  <xsl:template match="link[@rel= 'rfc']">
    <a href="http://developer.xp-framework.net/xml/rfc/view?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to another topic
   !-->
  <xsl:template match="link[@rel= 'topic']">
    <a href="{xp:link(concat($__state, '?', @href))}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a PHP manual page
   !-->
  <xsl:template match="link[@rel= 'php']">
    <a href="http://de3.php.net/{@href}"><xsl:value-of select="."/></a>
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
