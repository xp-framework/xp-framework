<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
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
  <xsl:include href="../layout.xsl"/>
  <xsl:include href="../calendar.inc.xsl"/>
  <xsl:include href="../entry.inc.xsl"/>
    
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
    <xsl:variable name="planetarium">
      <planet url="http://planet-php.net">Planet PHP</planet>
      <planet url="http://www.planetapache.org">Planet Apache</planet>
      <planet url="http://www.go-mono.com/monologue">Mono-Logue</planet>
      <planet url="http://planetjava.org/">PlanetJava</planet>
    </xsl:variable>

    <xsl:call-template name="calendar">
      <xsl:with-param name="month" select="/formresult/month"/>
    </xsl:call-template>
    <br clear="all"/>  
    
    <!-- Planetarium -->
    <table class="sidebar" border="0" width="180" cellspacing="0" cellpadding="0">
      <tr>
        <td class="sidebar_head">
          Planetarium
        </td>
      </tr>
      <xsl:for-each select="exsl:node-set($planetarium)/planet">
        <tr>
          <td valign="top">
            <nobr>
              <a href="/deref/?{@url}"><xsl:value-of select="."/></a>
            </nobr>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    <br clear="all"/>

    <!-- Listed feed -->
    <table class="sidebar" border="0" width="180" cellspacing="0" cellpadding="0">
      <tr>
        <td class="sidebar_head">
          Listed Blogs
        </td>
      </tr>
      <xsl:for-each select="/formresult/feeds/feed">
        <tr>
          <td valign="top">
            <xsl:variable name="title">
              <xsl:if test="string-length(@title) &gt; 35"><xsl:value-of select="concat(substring(@title, 0, 35), '...')"/></xsl:if>
              <xsl:if test="string-length(@title) &lt;= 35"><xsl:value-of select="@title"/></xsl:if>
            </xsl:variable>
            <nobr>
              <a href="/deref/?{@link}"><xsl:value-of select="$title" title="{@author}"/></a>
            </nobr>
          </td>
        </tr>
      </xsl:for-each>
    </table>
  </xsl:template>
  
  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="offset" select="/formresult/offset"/>
    <xsl:variable name="items" select="/formresult/syndicate/item"/>
  
    <!-- Headline -->
    <h2>Latest entries from #<xsl:value-of select="$offset + 1"/> to #<xsl:value-of select="$offset + 9"/></h2>
    
    <xsl:for-each select="exsl:node-set($items)">

      <!-- Display date when this is the first item or its date differs from the previous item -->
      <xsl:variable name="pos" select="position()"/>
      <xsl:if test="$pos &lt;= 1 or ($items[$pos - 1]/published/year != published/year or $items[$pos - 1]/published/yday != published/yday)">
        <h2 class="date" align="right"><xsl:value-of select="func:smartdate(published)"/></h2>
      </xsl:if>

      <xsl:call-template name="display-entry">
        <xsl:with-param name="entry" select="$items[$pos]"/>
      </xsl:call-template>
    </xsl:for-each>
    
    <xsl:if test="$offset &gt;= 10">
      <a href="{$__state}?offset={$offset - 10}">Previous 10</a>
    </xsl:if>
    |
    <a href="{$__state}?offset={$offset + 10}">Next 10</a>
  </xsl:template>

</xsl:stylesheet>
