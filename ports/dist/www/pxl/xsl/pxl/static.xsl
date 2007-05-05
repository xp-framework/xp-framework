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
  <xsl:include href="layout.inc.xsl"/>
  
  <xsl:template name="page-body">
    <xsl:apply-templates select="/formresult/page"/>
  </xsl:template>

  <xsl:template match="page">
    <xsl:call-template name="pager"/>

    <div id="picturecontainer">
      <xsl:apply-templates select="pictures/picture"/>
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
        <xsl:when test="'' != prev/@id">
          <a class="navigator" href="{concat('/story/', prev/@id, '/', prev/@title)}">previous</a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-prev"/>
        </xsl:otherwise>
      </xsl:choose>

      |

      <xsl:choose>
        <xsl:when test="'' != next/@id">
          <a class="navigator" href="{concat('/story/', next/@id, '/', next/@title)}">next</a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-next"/>
        </xsl:otherwise>
      </xsl:choose>
    </div>
  </xsl:template>
  
  <xsl:template match="picture">
    <div class="picture" style="width: {dimensions/@width}px">
      <img src="{concat('/pages/', ../../@id, '/', filename)}"/>
      <xsl:apply-templates select="exif"/>
    </div>
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
    <a href="#" class="navigator_disabled">previous</a>
  </xsl:template>

  <xsl:template name="empty-next">
    <a href="#" class="navigator_disabled">next</a>
  </xsl:template>
</xsl:stylesheet>
