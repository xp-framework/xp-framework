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
  
  <xsl:template name="hierarchy">
    <xsl:param name="path"/>
    <xsl:param name="base" select="''"/>
    <xsl:variable name="chunk" select="substring-before($path, ',')"/>
    <xsl:variable name="rest" select="substring-after($path, ',')"/>
    
    <a href="{xp:link(concat('browse?', $base, $chunk))}">
      <xsl:value-of select="$chunk"/>
    </a>
    <xsl:if test="$rest">
      &#xbb;
      <xsl:call-template name="hierarchy">
        <xsl:with-param name="path" select="$rest"/>
        <xsl:with-param name="base" select="concat($base, $chunk, ',')"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>
  
  <!-- Collections -->
  <xsl:template match="collection">
    <td>
      <img align="left" width="22" height="22" src="/image/icons/collection.png"/>
      <a href="{xp:link(concat('browse?', $__query, ',', name))}">
        <b><xsl:value-of select="name"/></b>
      </a>
    </td>
    <td>
    </td>
  </xsl:template>

  <!-- Elements -->
  <xsl:template match="element">
    <td>
      <img align="left" width="22" height="22" src="/image/icons/{translate(mime, '/', '_')}.png"/>
      <a href="#"><xsl:value-of select="name"/></a>
    </td>
    <td>
      <xsl:value-of select="modified"/>
    </td>
  </xsl:template>

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
        
          <!-- Breadcrumb -->
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Experiments</a> &#xbb;
            <xsl:call-template name="hierarchy">
              <xsl:with-param name="path" select="concat(/formresult/list/@path, ',')"/>
            </xsl:call-template>
          </div>
          <br/>
        
          <!-- List: Collections first -->
          <h2>Elements</h2>
          <table class="dir" cellspacing="1">
            <colgroup span="2">
              <col width="70%"/>  <!-- Name -->
              <col width="30%"/>  <!-- Modified -->
            </colgroup>
            <xsl:for-each select="/formresult/list/*">
              <xsl:sort select="name()"/>

              <tr class="row{position() mod 2}">
                <xsl:apply-templates select="."/>
              </tr>
            </xsl:for-each>
          </table>
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
