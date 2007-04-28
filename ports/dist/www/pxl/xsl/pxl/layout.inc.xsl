<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: static.xsl 7662 2006-08-13 11:47:23Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:output method="xml" encoding="utf-8"/>
  <xsl:include href="../master.xsl"/>
  
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:value-of select="/formresult/config/title"/>
          
          <xsl:if test="/formresult/page/@title != ''">
           - <xsl:value-of select="/formresult/page/@title"/>
          </xsl:if>
        </title>
        <link rel="stylesheet" href="/styles/default.css"/>
      </head>
      <body>
        <xsl:call-template name="page-header">
          <xsl:with-param name="extra" select="/formresult/page/@title"/>
        </xsl:call-template>
        <div id="content">
          <xsl:call-template name="page-body"/>
        </div>
      </body>
    </html>
  </xsl:template>
  
  <xsl:template name="page-header">
    <xsl:param name="extra" select="''"/>
    
    <div id="header">
      <a class="title" href="/"><xsl:value-of select="/formresult/config/title"/></a>
      <xsl:if test="$extra != ''">
        <div class="title"><xsl:value-of select="$extra"/></div>
      </xsl:if>
    </div>
  </xsl:template>
  
</xsl:stylesheet>
