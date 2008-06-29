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

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <h1>Experiments</h1>
          <p>
            This is where experimental stuff should go, such as patches, case
            studies, suggestions or scripts that may be of use.
          </p>
          <br/><br clear="all"/>
          
          <h2><a href="{xp:link('browse?arena')}">Arena</a></h2>
          <p>
            Show-off stuff which could be of interest for everyone.
          </p>
          <br/><br clear="all"/>

          <h2><a href="{xp:link('browse?people')}">People</a></h2>
          <p>
            This is where your own experiments go. Everything which you 
            would consider "private" and of minor (or no) particular interest 
            for the rest.
          </p>
          <br/><br clear="all"/>
        </td>
        <td id="context">
          <h3>Table of contents</h3>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Forge Experiments
  </xsl:template>
  
</xsl:stylesheet>
