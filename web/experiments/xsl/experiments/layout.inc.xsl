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
  <xsl:include href="../../../common/xsl/layout.inc.xsl"/>
  
  <xsl:variable name="navigation">
    <area name="forge"/>
  </xsl:variable>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>

  <xsl:template name="tracking-code">UA-617805-6</xsl:template>

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
  
</xsl:stylesheet>
