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
  
  <xsl:template match="a">
    <a href="/deref/?{@href}"><xsl:apply-templates select="./*|text()"/></a>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="offset" select="0"/>

    <h1>Latest entries from #<xsl:value-of select="$offset + 1"/> to #<xsl:value-of select="$offset + 9"/></h1>

    <!-- 
    <xsl:variable name="shortcuts">
      <shortcut href="about/topic?introduction" icon="introduction">Introduction</shortcut>
      <shortcut href="about/examples" icon="examples">Examples</shortcut>
      <shortcut href="resources" icon="download">Download</shortcut>
    </xsl:variable>
    <xsl:copy-of select="func:shortcuts(exsl:node-set($shortcuts))"/>
    -->
    
    <xsl:for-each select="/formresult/syndicates/syndicate/item">
      <xsl:if test="position() &gt;= $offset and position() &lt;= 10">
        <div class="entry entryclass-{feed/@feed_id}">
          <h3>
            <a href="/deref/?{feed/@link}">
              <xsl:value-of select="./@author"/>: <xsl:value-of select="./@title"/>
            </a>
          </h3>
          <p>
            <xsl:apply-templates select="content"/>
          </p>
        </div>
        <br clear="all"/>
      </xsl:if>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
