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
          <a href="{concat(xp:linkCategory(/formresult/categories/category[@current-category= 'true']/@id, /formresult/categories/category[@current-category= 'true']/@link), '?', @next)}">&gt;&gt;&gt;</a>
        </xsl:when>
        <xsl:otherwise>&gt;&gt;&gt;</xsl:otherwise>
      </xsl:choose>
    </div>
  </xsl:template>
  
  <xsl:template name="context-feed">
    <xsl:variable name="feed">/rss/<xsl:if test="/formresult/categories/category[@current-category= 'true'] and /formresult/categories/category[@current-category= 'true']/@id != 8">?c=<xsl:value-of select="/formresult/categories/category[@current-category= 'true']/@id"/></xsl:if>
    </xsl:variable>
    <h3>
      <a href="{$feed}">
        <img align="right" src="/common/image/feed.png" border="0"/>
      </a>
      Subscribe
    </h3>
    You can subscribe to the XP framework's news by using <a href="{$feed}">RSS syndication</a>.
    <br clear="all"/>
  </xsl:template>
  
  <xsl:template match="categories">
    <h3>Categories</h3>
    <xsl:apply-templates select="category[@id= 8]"/>
  </xsl:template>
  
  <xsl:template match="category">
    <xsl:param name="depth" select="0"/>
    <xsl:variable name="id" select="@id"/>
    
    <a href="{xp:linkCategory(@id, @link)}">
      <xsl:if test="$depth &gt; 0">
        <xsl:attribute name="style"><xsl:value-of select="concat('padding-left: ', $depth * 6, 'px;')"/></xsl:attribute>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="@current-category= 'true'"><b><xsl:value-of select="."/></b></xsl:when>
        <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
      </xsl:choose>
    </a><br/>
    <xsl:apply-templates select="../category[@parentid= $id]">
      <xsl:with-param name="depth" select="$depth+ 1"/>
    </xsl:apply-templates>
  </xsl:template>  
</xsl:stylesheet>