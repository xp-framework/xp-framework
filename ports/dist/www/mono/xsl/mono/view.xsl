<?xml version="1.0" encoding="utf-8"?>
<!-- 
 ! View pictures
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
  <xsl:import href="layout.xsl"/>
  <xsl:include href="../date.inc.xsl"/>

  <xsl:template name="page-title">
    <xsl:value-of select="/formresult/picture/title"/> - <xsl:value-of select="/formresult/config/general/site"/>
  </xsl:template>

  <xsl:template name="contents">
    <div id="container">
      <div id="header">
        <!-- ... -->
        <div id="header-nav">
          <a href="/{/formresult/navigation/@latestdate}">latest</a> |
          <a href="{func:link('about')}">about</a> |
          <a href="{func:link('search')}">search</a> |
          <a href="/rss2/">rss2</a> | 
          <a href="{func:link('links')}">links</a>
        </div>
      </div>
      
      <div id="picture-frame">
        <xsl:call-template name="view-prev-next"/>
        <div id="picture-inner-frame">
          <xsl:choose>
            <xsl:when test="/formresult/navigation/@prevdate != ''">
              <a href="/{/formresult/navigation/@prevdate}">
                <xsl:call-template name="view-picture"/>
              </a>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="view-picture"/>
            </xsl:otherwise>
          </xsl:choose>
        </div>
        <p id="picture-information">
          <xsl:apply-templates select="/formresult/picture/exif"/>
        </p>
        <p id="picture-description">
          <xsl:if test="not(/formresult/description)">
            There is no description for this picture
          </xsl:if>
          
          <xsl:if test="/formresult/description">
            <xsl:copy-of 
             select="/formresult/description"
             disable-output-escaping="yes"
            />
          </xsl:if>
        </p>
        <xsl:call-template name="view-prev-next"/>
      </div>
    </div>
  </xsl:template>
  
  <xsl:template name="view-picture">
    <img
     id="picture"
     src="/shots/{/formresult/navigation/@currentid}/{/formresult/picture/filename}"
     width="{/formresult/picture/width}"
     height="{/formresult/picture/height}"
    />
  </xsl:template>
  
  <xsl:template match="exif">
    <xsl:value-of select="model"/> | 
    <xsl:value-of select="apertureFNumber"/> |
    <xsl:value-of select="exposureTime"/>s | 
    iso <xsl:value-of select="isoSpeedRatings"/> |
    <xsl:value-of select="func:datetime(dateTime)"/>
  </xsl:template>
  
  <xsl:template name="view-prev-next">
    <!-- Link to previous picture -->
    <div id="prev-next">
      <a>
        <xsl:if test="/formresult/navigation/@prevdate = ''"><xsl:attribute name="class">link_disabled</xsl:attribute></xsl:if>
        <xsl:if test="/formresult/navigation/@prevdate != ''"><xsl:attribute name="href">/<xsl:value-of select="/formresult/navigation/@prevdate"/></xsl:attribute></xsl:if>
        &#171; day before
      </a>
      &#160;|&#160;
      <!-- Link to next picture -->
      <a>
        <xsl:if test="/formresult/navigation/@nextdate = ''"><xsl:attribute name="class">link_disabled</xsl:attribute></xsl:if>
        <xsl:if test="/formresult/navigation/@nextdate != ''"><xsl:attribute name="href">/<xsl:value-of select="/formresult/navigation/@nextdate"/></xsl:attribute></xsl:if>
        day after &#187;
      </a>
    </div>
  </xsl:template>
</xsl:stylesheet>
