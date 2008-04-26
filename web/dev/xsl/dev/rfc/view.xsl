<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC view page
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
  
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
            <a href="{xp:link('rfc')}">RFCs</a> &#xbb;
            <a href="{xp:link(concat('rfc/view?', /formresult/rfc/@number))}">#<xsl:value-of select="/formresult/rfc/@number"/></a>
          </div>
          
          <h1>
            <xsl:value-of select="/formresult/rfc/title"/>
          </h1>

          <fieldset class="summary">
            <img src="/image/{/formresult/rfc/status/@id}.png" widht="16" height="16"/>
            <b><xsl:value-of select="/formresult/rfc/status"/></b>
          </fieldset>
          
          <br/><br clear="all"/>
          <xsl:apply-templates select="/formresult/rfc/content"/>
          <br clear="all"/>
        </td>
        <td id="context">
          Table of contents
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Framework Developer Zone - RFCs
  </xsl:template>
  
</xsl:stylesheet>
