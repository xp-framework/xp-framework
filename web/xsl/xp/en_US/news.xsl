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
   ! @see      ../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>News</h1>

    <xsl:for-each select="/formresult/news/item">
      <h3>
        <xsl:value-of select="caption"/> (<xsl:value-of select="func:datetime(created_at)"/>)
      </h3>
      <p>
        <xsl:apply-templates select="excerpt"/>
        &#160;
        <a href="news/view?{news_id}">More</a>
      </p>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
