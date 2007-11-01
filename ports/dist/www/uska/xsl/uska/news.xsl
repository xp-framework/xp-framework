<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
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

  <xsl:include href="layout.xsl"/>
  <xsl:include href="../news.inc.xsl"/>
  
  <xsl:template name="context">
    <xsl:if test="func:hasPermission('create_news') != ''">
      <xsl:call-template name="default_subnavigation">
        <xsl:with-param name="items">
          <item href="http://cms.uska.de">Artikel-Editor öffnen</item>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>
  
  <!--
   ! Template that matches on an entry
   !
   ! @purpose  Define the layout for an entry
   !-->
  <xsl:template match="entry">
    <div class="entry">
      <h3>
        <a href="{func:link(concat('news/view?', @id))}">
          <xsl:value-of select="title"/>
        </a>
      </h3>
      <p>
        <xsl:apply-templates select="body"/>
        <xsl:if test="extended_length &gt; 0">
          &#160; ... <a href="{func:link(concat('news/view?', @id))}" title="Ganzen Artikel lesen"><b>(weiterlesen)</b></a>
        </xsl:if>
      </p>
      <em>
        Geschrieben von <xsl:value-of select="author"/> in 
        <xsl:for-each select="category">
          <a href="{func:link(concat('news/bycategory?', @id))}">
            <xsl:value-of select="."/>
          </a>
          <xsl:choose>
            <xsl:when test="position() = last()"/>
            <xsl:when test="position() = last() - 1"> und </xsl:when>
            <xsl:otherwise>, </xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>        
        am <xsl:value-of select="func:datetime(date)"/>
      </em>
    </div>
  </xsl:template>

  <xsl:template name="content">
    <xsl:variable name="entries" select="/formresult/entries/entry"/>

    <xsl:for-each select="exsl:node-set($entries)">
      <xsl:variable name="pos" select="position()"/>
      <xsl:if test="$pos = 1 or (func:date($entries[$pos - 1]/date) != func:date(date))">
        <h3 class="date">
          <xsl:copy-of select="func:smartdate(date)"/>
        </h3>
      </xsl:if>
      <xsl:apply-templates select="."/>
      <br clear="all"/>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
