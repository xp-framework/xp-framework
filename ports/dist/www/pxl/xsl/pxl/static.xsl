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
  
  <xsl:template name="static-links">
    <div id="static-links">
      <a href="{latest/@link}">latest shot</a> /
      <a href="/static/about">about</a> /
      <a href="/static/links">links</a> /
      <a href="#">archives</a> /
      <a href="/feeds/rss">rss feed</a>
    </div>
  </xsl:template>

  <xsl:template match="page">
    <xsl:call-template name="static-links"/>
    <xsl:call-template name="pager"/>

    <div id="picturecontainer">
      <xsl:apply-templates select="pictures/picture"/>
      <div id="picturedescription">
        <div id="page-description">
          <div id="page-calendar">
            <div id="page-daymonth"><xsl:value-of select="concat(published/mday, '/', published/mon)"/></div>
            <div id="page-year"><xsl:value-of select="published/year"/></div>
          </div>
          <xsl:apply-templates select="description"/>
        </div>
      </div>
    </div>
  </xsl:template>
  
  <xsl:template name="pager">
    <div id="pager">
      <xsl:choose>
        <xsl:when test="'' != prev/@link">
          <a class="navigator" href="{prev/@link}">previous</a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-prev"/>
        </xsl:otherwise>
      </xsl:choose>

      |

      <xsl:choose>
        <xsl:when test="'' != next/@link">
          <a class="navigator" href="{next/@link}">next</a>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="empty-next"/>
        </xsl:otherwise>
      </xsl:choose>
    </div>
  </xsl:template>
  
  <xsl:template match="picture">
    <div class="picture">
      <xsl:choose>
        <xsl:when test="'' != ../../prev/@link">
          <a href="{../../prev/@link}">
            <img src="{concat('/pages/', ../../@id, '/', filename)}" border="0"/>
          </a>
        </xsl:when>
        <xsl:otherwise>
          <img src="{concat('/pages/', ../../@id, '/', filename)}"/>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:apply-templates select="exif"/>
    </div>
  </xsl:template>
  
  <xsl:template match="exif">
    <div id="picture-exif">
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
