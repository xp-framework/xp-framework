<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Search
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
  <xsl:variable name="searchquery" select="/formresult/formvalues/param[@name= 'query']"/>
  
  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
  </xsl:template>

  <xsl:template name="html-title">
    Search for "<xsl:value-of select="$searchquery"/>" - XP Framework
  </xsl:template>

  <xsl:template match="pager">
    <div class="pager">
      <a title="Newer entries" class="pager{@offset &gt; 0}" id="previous">
        <xsl:if test="@offset &gt; 0">
          <xsl:attribute name="href"><xsl:value-of select="concat(xp:link($__state), '?query=', $searchquery, '&amp;offset=', @prev)"/></xsl:attribute>
        </xsl:if>
        &#xab;
      </a>
      <a title="Older entries" class="pager{@next != ''}" id="next">
        <xsl:if test="@next">
          <xsl:attribute name="href"><xsl:value-of select="concat(xp:link($__state), '?query=', $searchquery, '&amp;offset=', @next)"/></xsl:attribute>
        </xsl:if>
        &#xbb;
      </a>
    </div>
  </xsl:template>

  <!-- Everything until the end of the first sentence -->
  <func:function name="func:first-sentence">
    <xsl:param name="text"/>
    <xsl:variable name="normalized" select="concat($text, '. ')"/>
    <xsl:variable name="dot" select="substring-before($normalized, '. ')"/>
    <xsl:variable name="new" select="substring-before($normalized, '.&#10;')"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="string-length($new) &lt; string-length($dot)">
          <xsl:value-of select="$new"/>
        </xsl:when>
        <xsl:when test="string-length($dot) &lt; string-length($normalized)">
          <xsl:value-of select="$dot"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$normalized"/>
        </xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
  
  <xsl:template match="searchresult/item[@type= 'newsentries']">
    <h3>
      <a href="http://news.xp-framework.net/article/{@id}/0000/00/00/">
        <xsl:value-of select="title"/>
      </a>
    </h3>
    <p>
      <xsl:value-of select="func:first-sentence(summary)" disable-output-escaping="yes"/>.<br/>
      <em>From the news</em>
    </p>
  </xsl:template>

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content">
        <h1>Search results</h1>

        <xsl:apply-templates select="/formresult/pager"/>

        <xsl:for-each select="/formresult/searchresult/item">
          <xsl:apply-templates select="."/>
          <br clear="all"/>
        </xsl:for-each>

        <xsl:apply-templates select="/formresult/pager"/>
      </td>
    </tr></table>
  </xsl:template>
</xsl:stylesheet>
