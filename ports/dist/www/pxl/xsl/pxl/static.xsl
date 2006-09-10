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

  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:value-of select="/formresult/config/title"/> - 
          <xsl:value-of select="/formresult/current/name"/>
        </title>
        <link rel="stylesheet" href="/styles/default.css"/>
      </head>
      <body>
        <xsl:apply-templates select="/formresult/page"/>
        
        <a href="/xml/pxl.en_US/admin">Admin</a>
      </body>
    </html>
  </xsl:template>
  
  <xsl:template match="page">
    <div id="header">
      <xsl:apply-templates select="../prev"/>
      <xsl:apply-templates select="../next"/>
    </div>

    <div id="picturecontainer">
      <xsl:apply-templates select="picture"/>
      <div id="picturedescription">
        <xsl:apply-templates select="description"/>
      </div>
    </div>
  </xsl:template>
  
  <xsl:template match="picture">
    <div id="picture">
      <img src="{concat('/pages/', ../../current/path, '/', @filename)}"/>
    </div>
  </xsl:template>
  
  <xsl:template match="prev">
    <a href="{concat('/story/', id, '-', name)}">Previous</a>
  </xsl:template>
  
  <xsl:template match="next">
    <a href="{concat('/story/', id, '-', name)}">Next</a>
  </xsl:template>
</xsl:stylesheet>
