<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
 !
 ! $Id: master.xsl 4410 2004-12-18 18:19:28Z friebe $
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
  <xsl:include href="links.inc.xsl"/>
  
  <xsl:variable name="navigation">
    <area name="news"/>
  </xsl:variable>

  <xsl:template name="tracking-code">UA-617805-6</xsl:template>

  <xsl:template match="pager">
    <div style="text-align: center;">
      <xsl:choose>
        <xsl:when test="@offset &gt; 0">
          <a href="{concat(xp:linkCategory(/formresult/current-category/@id, /formresult/current-category/@link), '?', @prev)}">&lt;&lt;&lt;</a>
        </xsl:when>
        <xsl:otherwise>&lt;&lt;&lt;</xsl:otherwise>
      </xsl:choose>
      |
      <xsl:choose>
        <xsl:when test="@next">
          <a href="{concat(xp:linkCategory(/formresult/current-category/@id, /formresult/current-category/@link), '?', @next)}">&gt;&gt;&gt;</a>
        </xsl:when>
        <xsl:otherwise>&gt;&gt;&gt;</xsl:otherwise>
      </xsl:choose>
    </div>
  </xsl:template>
</xsl:stylesheet>