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
  <xsl:include href="../../../common/xsl/markup.inc.xsl"/>
  
  <!-- Collections -->
  <xsl:template match="list/collection">
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
  <xsl:template match="list/element">
    <td>
      <img align="left" width="22" height="22" src="/image/icons/{translate(mime, '/', '_')}.png"/>
      <a href="{xp:link(concat('view?', $__query, ',', name))}"><xsl:value-of select="name"/></a>
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

          <!-- Show README file -->
          <xsl:if test="/formresult/readme">
            <xsl:apply-templates select="/formresult/readme"/>
           <br/><br clear="all"/>
          </xsl:if>
        
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
          <a href="{xp:link('browse?arena')}">Arena</a><br/>
          <a href="{xp:link('browse?people')}">People</a><br/>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Forge Experiments
  </xsl:template>
  
</xsl:stylesheet>
