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
  <xsl:include href="rfc/criteria.inc.xsl"/>
  
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content">
        <div id="breadcrumb">
          <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
          <a href="{xp:link('rfc')}">RFCs</a>
        </div>

        <h1>RFCs</h1>
        <br clear="all"/>

        <!-- Featured items -->
        <table width="100%" class="columned"><tr>
          <td width="50%" valign="top">
            <h2>Current RFCs</h2>
            <br clear="all"/>
            <xsl:for-each select="/formresult/list/rfc">
              <a href="{xp:link(concat('rfc/view?', @number))}">
                <h3>
                  <img border="0" src="/image/{status/@id}.png" width="16" height="16"/>
                  #<xsl:value-of select="@number"/>: <xsl:value-of select="title"/>
                </h3>
              </a>
              <div style="margin-bottom: 18px;">
                <em><xsl:value-of select="created"/></em>
                <xsl:apply-templates select="scope/p[2]"/>
              </div>
            </xsl:for-each>
          </td>
          <td width="25%" valign="top">
            <h2>Browse RFCs</h2>
            <xsl:for-each select="exsl:node-set($criteria)/criteria">
              <br clear="all"/>
              <p>By <xsl:value-of select="@id"/>:</p>
              <ul>
                <xsl:for-each select="filter">
                  <li><a href="{xp:link(concat('rfc/list?status.', @id))}"><xsl:value-of select="."/></a></li>
                </xsl:for-each>
              </ul>
            </xsl:for-each>
          </td>
          <td width="25%" valign="top">
            <h2>More</h2>
            <br clear="all"/>
            <ul>
              <li><a href="#">The RFC process</a></li>
            </ul>
          </td>
        </tr></table>
        <br clear="all"/>
      </td>
    </tr></table>
  </xsl:template>

  <xsl:template name="html-title">
    RFCs - XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
