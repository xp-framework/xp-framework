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
  <xsl:include href="../news.inc.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
    <!-- Categories -->
    <h4 class="context">Categories</h4>
    <table border="0">
      <xsl:for-each select="/formresult/categories/category">
        <xsl:sort key="."/>
        <tr>
          <td width="1%" valign="top">
            <a href="/rss/?{@id}">
              <img src="/image/rss.png" vspace="1" border="0" height="15" width="27" alt="RSS"/>
            </a>  
          </td>
          <td valign="top">
            <a class="category{@id}" href="category?{@id}"><xsl:value-of select="."/></a>
          </td>
        </tr>
      </xsl:for-each>
      <tr>
        <td width="1%">
          <a href="/rss/">
            <img src="/image/rss.png" border="0" height="15" width="27" alt="RSS"/>
          </a>  
        </td>
        <td>
          <a class="allcategories" href="home">All categories</a>
        </td>
      </tr>
    </table>
  </xsl:template>

  <!--
   ! Template that matches on an entry
   !
   ! @purpose  Define the layout for an entry
   !-->
  <xsl:template match="entry">
    <div class="entry">
      <h3>
        <a href="news/view?{@id}">
          <xsl:value-of select="title"/>
        </a>
      </h3>
      <p>
        <xsl:apply-templates select="body"/>
        <xsl:if test="extended_length &gt; 0">
          &#160; ... <a href="news/view?{@id}" title="View extended entry"><b>(more)</b></a>
        </xsl:if>
      </p>
      <em>
        Posted by <xsl:value-of select="author"/> 
        at <xsl:value-of select="func:datetime(date)"/>
      </em>
    </div>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="entries" select="/formresult/entries/entry"/>

    <h1>Newest entries</h1>    
    <xsl:for-each select="exsl:node-set($entries)">
      <xsl:variable name="pos" select="position()"/>
      <xsl:if test="$pos = 1 or exsl:node-set($entries[$pos - 1])/date/yday != ./date/yday">
        <h2>
          <xsl:value-of select="func:date(date)"/>
        </h2>
      </xsl:if>
      <xsl:apply-templates select="."/>
      <br clear="all"/>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
