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
  
    <!-- Other Planet-Sites -->
    <h4 class="context">Planetarium</h4>
    <ul class="context">
      <xsl:for-each select="/formresult/news/items/item">
        <li>
          <em><xsl:value-of select="func:datetime(created_at)"/></em>:<br/>
          <a href="news/view?{news_id}">
            <xsl:value-of select="caption"/>
          </a>
        </li>
      </xsl:for-each>
    </ul>

    <!-- Listed Feeds -->
    <h4 class="context">Read more ...</h4>
    <ul class="context">
      <xsl:for-each select="/formresult/syndication/syndicate/feed">
        <li>
          <em>...</em>:<br/>
          <a href="/deref/?{@link}"><xsl:value-of select="@title"/></a>
        </li>
      </xsl:for-each>
    </ul>

    <!-- release -->
    <h4 class="context">Current release</h4>
    <ul class="context">
      <li>
        <em>2003-10-26</em>:<br/>
        <a href="#release/2003-10-26">Download</a> | <a href="#changelog/2003-10-26">Changelog</a>
      </li>
    </ul>
  </xsl:template>
  
  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="offset" select="/formresult/offset"/>
    <xsl:variable name="items" select="/formresult/syndicates/syndicate/item"/>
  
    <!-- Headline -->
    <h2>Latest entries from #<xsl:value-of select="$offset + 1"/> to #<xsl:value-of select="$offset + 9"/></h2>
    
    <xsl:for-each select="exsl:node-set($items)">
      <xsl:if test="position() &gt;= $offset and position() &lt; $offset + 10">
      
        <!-- Display date when this is the first item or its date differs from the previous item -->
        <xsl:variable name="pos" select="position()"/>
        <xsl:if test="position() = $offset or ($items[$pos - 1]/published/year != published/year or $items[$pos - 1]/published/yday != published/yday)">
          <h2 class="date" align="right"><xsl:value-of select="func:smartdate(published)"/></h2>
        </xsl:if>
        
        <xsl:call-template name="display-entry">
          <xsl:with-param name="entry" select="$items[$pos]"/>
        </xsl:call-template>
        
      </xsl:if>
    </xsl:for-each>
    
    <xsl:if test="$offset &gt;= 10">
      <a href="{$__state}?offset={$offset - 10}">Previous 10</a>
    </xsl:if>
    |
    <xsl:if test="($offset + 10) &lt; count(/formresult/syndicates/syndicate/item)">
      <a href="{$__state}?offset={$offset + 10}">Next 10</a>
    </xsl:if>
  </xsl:template>
  
</xsl:stylesheet>
