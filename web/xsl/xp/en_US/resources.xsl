<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for resources page
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
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">

    <!-- see also -->
    <h4 class="context">See also</h4>
    <ul class="context">
      <li>
        <em>cvsweb</em>:<br/>
        <a href="http://cvs.xp-framework.net/" target="_cvs">Browse CVS repository<img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/></a>
      </li>
    </ul>

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
        Posted by <xsl:value-of select="author"/> in 
        <xsl:for-each select="category">
          <a href="news/bycategory?{@id}">
            <xsl:value-of select="."/>
          </a>
          <xsl:choose>
            <xsl:when test="position() = last()"/>
            <xsl:when test="position() = last() - 1"> and </xsl:when>
            <xsl:otherwise>, </xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>        
        at <xsl:value-of select="func:datetime(date)"/>
        (<xsl:value-of select="num_comments"/> comments)
      </em>
    </div>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="entries" select="/formresult/entries/entry"/>
    <h1>resources</h1>

    <h3>
      current releases
    </h3>

    <xsl:for-each select="exsl:node-set($entries)">
      <xsl:variable name="pos" select="position()"/>
      <xsl:if test="$pos = 1 or exsl:node-set($entries[$pos - 1])/date/yday != ./date/yday">
        <h2 class="date">
          <xsl:value-of select="func:date(date)"/>
        </h2>
      </xsl:if>
      <xsl:apply-templates select="."/>
      <br clear="all"/>
    </xsl:for-each>

    <!-- anoncvs -->
    <h3>anonymous cvs</h3>
    <p>
      We now offer an anonymous cvs access. Check it out with:
      <pre>cvs -d:pserver:anonymous@php3.de:/home/cvs/repositories/xp co .</pre>
      (Password is empty).
    </p>
  </xsl:template>
  
</xsl:stylesheet>
