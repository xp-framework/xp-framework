<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! User documentation
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
  <xsl:include href="doc.inc.xsl"/>

  <xsl:variable name="breadcrumb">
    <title for="core">Core concepts</title>
    <title for="topics">Topics</title>
    <title for="setup">Setup</title>
  </xsl:variable>

  <xsl:template name="content">
    <xsl:variable name="base" select="/formresult/documentation/@base"/>

    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Documentation</a> &#xbb;
            <a href="{xp:link(concat('doc?', $base))}">
              <xsl:value-of select="exsl:node-set($breadcrumb)/title[@for= $base]"/>
            </a>
            <xsl:if test="/formresult/documentation/@topic"> &#xbb;
              <a href="{xp:link(concat('doc?', $base, '/', /formresult/documentation/@topic))}">
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
    <xsl:value-of select="/formresult/documentation/h1"/> - 
    XP Framework Documentation
  </xsl:template>
</xsl:stylesheet>
