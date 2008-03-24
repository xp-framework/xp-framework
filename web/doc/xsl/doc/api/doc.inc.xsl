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
 xmlns:str="http://exslt.org/strings"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="../layout.inc.xsl"/>

  <xsl:template name="hierarchy">
    <xsl:param name="path"/>
    <xsl:variable name="nodes" select="str:tokenize($path, '.')"/>

    <xsl:for-each select="$nodes">
      <xsl:variable name="pos" select="position()"/>
      <a>
        <xsl:attribute name="href">
          <xsl:value-of select="xp:link('api/package?')"/>
          <xsl:for-each select="$nodes[position() &lt;= $pos]">
            <xsl:value-of select="."/>
            <xsl:if test="position() &lt; last()">.</xsl:if>
          </xsl:for-each>
        </xsl:attribute>
      <xsl:value-of select="."/></a>
      <xsl:if test="$pos &lt; last()">  &#xbb; </xsl:if>
    </xsl:for-each>
  </xsl:template>

  <func:function name="func:first-sentence">
    <xsl:param name="comment"/>
    
    <func:result>
      <xsl:value-of select="exsl:node-set(str:tokenize($comment, '.&#10;'))"/>
    </func:result>
  </func:function>
  
  <func:function name="func:cutstring">
    <xsl:param name="text"/>
    <xsl:param name="maxlength"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="string-length($text) &lt;= $maxlength">
          <xsl:value-of select="$text"/>
        </xsl:when>
        <xsl:otherwise>
          <span title="{$text}">
            <xsl:value-of select="substring($text, 1, $maxlength)"/>
            <b>[...]</b>
          </span>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <func:function name="func:typelink">
    <xsl:param name="type"/>
    
    <func:result>
      <a>
        <xsl:if test="contains($type, '.')">
          <xsl:attribute name="href">
            <xsl:value-of select="concat('?', string(exsl:node-set(str:tokenize(func:ltrim($type, '&amp;'), '[&amp;'))))"/>
          </xsl:attribute>
        </xsl:if>
        
        <xsl:value-of select="$type"/>
      </a>
    </func:result>
  </func:function>
  
  <xsl:template match="comment">
    <div class="apidoc">
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <!-- 
   ! Transform <code> ... </code> to <pre class="code"> ... </pre>
   ! because of IE's lacking support for white-space: pre
   !-->
  <xsl:template match="comment//code">
    <pre class="code">
      <xsl:apply-templates/>
    </pre>
  </xsl:template>

  <xsl:template match="comment//*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template match="see[@scheme = 'xp']">
    <a href="{xp:link(concat('api/class?', @href))}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'php']">
    <a href="http://php3.de/{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'http']">
    <a href="http://{@href}"><xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template match="see[@scheme = 'rfc']">
    <a href="http://faqs.org/rfcs/rfc{@href}.html">RFC #<xsl:value-of select="@href"/></a>
  </xsl:template>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>
 
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <xsl:apply-templates select="/formresult/doc/*"/>
        </td>
        <td id="context">
          <!-- -->
        </td>
      </tr>
    </table>
  </xsl:template>
  
  <xsl:template name="html-title">
    <xsl:value-of select="/formresult/doc/*/@name"/> - XP Framework Documentation
  </xsl:template>
</xsl:stylesheet>
