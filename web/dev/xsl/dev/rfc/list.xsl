<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC list page
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

  <xsl:include href="../layout.inc.xsl"/>
  <xsl:include href="list.inc.xsl"/>
  
  <xsl:variable name="criteria">
    <criteria id="status">
      <filter id="draft">Draft</filter>
      <filter id="discussion">Discussion</filter>
      <filter id="implemented">Implemented</filter>
      <filter id="obsoleted">Obsoleted</filter>
      <filter id="rejected">Rejected</filter>
    </criteria>
  </xsl:variable>

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
            <a href="{xp:link('rfc')}">RFCs</a>
          </div>
          
          <h1>By <xsl:value-of select="/formresult/list/@criteria"/>: <xsl:value-of select="/formresult/list/@filter"/> (<xsl:value-of select="/formresult/list/@count"/>)</h1>
          <xsl:call-template name="list">
            <xsl:with-param name="elements" select="/formresult/list"/>
          </xsl:call-template>
        </td>
        <td id="context">
          <h3>RFCs by status</h3>
          <xsl:for-each select="exsl:node-set($criteria)/criteria[@id= 'status']/filter">
            <a href="{xp:link(concat('rfc/list?status.', @id))}"><xsl:value-of select="."/></a><br/>
          </xsl:for-each>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    <xsl:value-of select="/formresult/list/@criteria"/> - RFCs - XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
