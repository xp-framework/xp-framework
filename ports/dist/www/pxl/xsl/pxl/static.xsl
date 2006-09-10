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
  <xsl:output
   method="xml"
   encoding="utf-8"
  />
  <xsl:include href="../date.inc.xsl"/>
  
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
      </body>
    </html>
  </xsl:template>
  
  <xsl:template match="page">
    <div id="header">
      <xsl:call-template name="pager"/>
    </div>

    <div id="picturecontainer">
      <xsl:apply-templates select="picture"/>
      <div id="picturedescription">
        <div class="description">
          <xsl:apply-templates select="description"/>
        </div>
      </div>
    </div>
  </xsl:template>
  
  <xsl:template name="pager">
    <div id="pager">
      <xsl:choose>
        <xsl:when test="'' != ../prev">
          <xsl:apply-templates select="../prev"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-prev"/>
        </xsl:otherwise>
      </xsl:choose>

      |

      <xsl:choose>
        <xsl:when test="'' != ../next">
          <xsl:apply-templates select="../next"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-next"/>
        </xsl:otherwise>
      </xsl:choose>
    </div>
  </xsl:template>
  
  <xsl:template match="picture">
    <div class="picture" style="width: {./@width}px">
      <img src="{concat('/pages/', ../../current/path, '/', @filename)}"/>
      <xsl:apply-templates select="exif"/>
    </div>
  </xsl:template>
  
  <xsl:template match="prev">
    <a class="navigator" href="{concat('/story/', id, '-', name)}">Previous</a>
  </xsl:template>
  
  <xsl:template match="next">
    <a  class="navigator" href="{concat('/story/', id, '-', name)}">Next</a>
  </xsl:template>
  
  <xsl:template match="exif">
    <div class="exif">
      <xsl:value-of select="concat(
        make, ' ', model, ' | ',
        apertureFNumber, ' | ',
        exposureTime, 's | ',
        'iso', isoSpeedRatings, ' @ ',
        func:datetime(dateTime)
      )"/>
    </div>
  </xsl:template>
  
  <xsl:template name="empty-prev">
    <a href="#" class="navigator_disabled">Previous</a>
  </xsl:template>

  <xsl:template name="empty-next">
    <a href="#" class="navigator_disabled">Next</a>
  </xsl:template>
</xsl:stylesheet>
