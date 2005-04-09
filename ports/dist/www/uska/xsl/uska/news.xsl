<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="layout.xsl"/>
  
  <xsl:template name="context">
    <table class="sidebar" cellpadding="0" cellspacing="0" width="170">
      <tr><td class="sidebar_head">Aktueller Punktestand</td></tr>
      <xsl:for-each select="/formresult/teams/team">
        <tr>
          <td>
            <a href="{func:link(concat('points?', team_id))}"><xsl:value-of select="name"/></a>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    <br/>
    
    <table class="sidebar" cellpadding="0" cellspacing="0" width="170">
      <tr><td class="sidebar_head">Das nächste ...</td></tr>
      <xsl:for-each select="/formresult/teams/team">
        <tr><td><a href="{func:link(concat('events?training,', team_id))}">Training <xsl:value-of select="name"/></a></td></tr>
      </xsl:for-each>
      
      <tr><td>&#160;</td></tr>
      
      <xsl:for-each select="/formresult/teams/team">
        <tr><td><a href="{func:link(concat('events?tournament,', team_id))}">Turnier <xsl:value-of select="name"/></a></td></tr>
      </xsl:for-each>
    </table>

  </xsl:template>
  
  <xsl:template name="content">
    <h3>Neueste News</h3>
  </xsl:template>
</xsl:stylesheet>
