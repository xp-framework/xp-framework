<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC Overview page
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
  
  <xsl:template name="list">
    <xsl:param name="elements"/>
    
    <xsl:for-each select="exsl:node-set($elements)">
      <xsl:sort select="@number" data-type="text" order="descending"/>
    
      <h2>
        <img src="/image/{status/@id}.png" widht="16" height="16"/>
        <a href="{xp:link(concat('rfc/view?', @number))}">
          #<xsl:value-of select="@number"/>: <xsl:value-of select="title"/>
        </a>
      </h2>
      <em>
        Created <xsl:value-of select="created"/> by <acronym title="{author/realname}"><xsl:value-of select="author/cn"/></acronym>,
        <b><xsl:value-of select="status"/></b>
      </em>
      <br/><br clear="all"/>
      <xsl:apply-templates select="content/p[2]"/>
      <br clear="all"/>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
            <a href="{xp:link('rfc')}">RFCs</a>
          </div>
          
          <h1>Currently under discussion</h1>
          <br clear="all"/>
          <xsl:call-template name="list">
            <xsl:with-param name="elements" select="/formresult/list/rfc[status/@id = 'discussion']"/>
          </xsl:call-template>

        </td>
        <td id="context">
          Table of contents
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    RFCs - XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
