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
  <xsl:include href="../../layout.xsl"/>
  <xsl:include href="../../news.inc.xsl"/>

  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1>
      <a href="{func:link('news')}">News</a> &#xbb; Entry #<xsl:value-of select="/formresult/entry/@id"/>
    </h1>
    <div class="entry">
      <h3>
        <a href="news/view?{/formresult/entry/@id}">
          <xsl:value-of select="/formresult/entry/title"/>
        </a>
      </h3>
      <p>
        <xsl:apply-templates select="/formresult/entry/body"/>
      </p>
      <p>
        <xsl:apply-templates select="/formresult/entry/extended"/>
      </p>
      <em>
        Posted by <xsl:value-of select="/formresult/entry/author"/> 
        at <xsl:value-of select="func:datetime(/formresult/entry/date)"/>
      </em>
    </div>
  </xsl:template>
  
</xsl:stylesheet>
