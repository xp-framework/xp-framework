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
 xmlns:php="http://php.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="exsl func php"
>
  <xsl:import href="../layout.xsl"/>
  
  <!--
   ! Template for page title
   !
   ! @see       ../layout.xsl
   !-->
  <xsl:template name="page-title">
    <xsl:choose>
      <xsl:when test="/formresult/selected/iptcData/title != ''">
        <xsl:value-of select="/formresult/selected/iptcData/title"/>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="concat('Image ', /formresult/selected/name)"/>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:value-of select="concat(
      ' / ',
      /formresult/album/@title, ' @ ', 
      /formresult/config/title
    )"/>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h3>
      <a href="{func:linkPage(0)}">Home</a> &#xbb; 

      <xsl:if test="/formresult/album/@page &gt; 0">
        <a href="{func:linkPage(/formresult/album/@page)}">
          Page #<xsl:value-of select="/formresult/album/@page"/>
        </a>
        &#xbb;
      </xsl:if>

      <xsl:if test="/formresult/album/collection">
        <a href="{func:linkCollection(/formresult/album/collection/@name)}">
          <xsl:value-of select="/formresult/album/collection/@title"/> Collection
        </a>
         &#xbb;
      </xsl:if>

      <a href="{func:linkAlbum(/formresult/album/@name)}">
        <xsl:value-of select="/formresult/album/@title"/>
      </a> 
      &#xbb; 
      <xsl:if test="/formresult/selected/@type = 'i'">
        <a href="{func:linkChapter(/formresult/album/@name, /formresult/selected/@chapter)}">
          Chapter #<xsl:value-of select="/formresult/selected/@chapter + 1"/>
        </a>
        &#xbb;
      </xsl:if>
      
      <xsl:choose>
        <xsl:when test="/formresult/selected/iptcData/title  != ''">
          <xsl:value-of select="/formresult/selected/iptcData/title"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="/formresult/selected/name"/>
        </xsl:otherwise>
      </xsl:choose>
    </h3>

    <br clear="all"/> 
    <center>
      <a title="Previous image" class="pager{/formresult/selected/prev != ''}" id="previous">
        <xsl:if test="/formresult/selected/prev != ''">
          <xsl:attribute name="href"><xsl:value-of select="func:linkImage(
            /formresult/album/@name,
            /formresult/selected/prev/chapter,
            /formresult/selected/prev/type,
            /formresult/selected/prev/number
          )"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Next image" class="pager{/formresult/selected/next != ''}" id="next">
        <xsl:if test="/formresult/selected/next != ''">
          <xsl:attribute name="href"><xsl:value-of select="func:linkImage(
            /formresult/album/@name,
            /formresult/selected/next/chapter,
            /formresult/selected/next/type,
            /formresult/selected/next/number
          )"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>
    
    <!-- Selected image -->
    <table border="0" width="800">
      <tr>
        <td id="image" align="center">
          <a>
            <xsl:if test="/formresult/selected/next != ''">
              <xsl:attribute name="href"><xsl:value-of select="func:linkImage(
                /formresult/album/@name,
                /formresult/selected/next/chapter,
                /formresult/selected/next/type,
                /formresult/selected/next/number
              )"/></xsl:attribute>
            </xsl:if>
            <img border="0" src="/albums/{/formresult/album/@name}/{/formresult/selected/name}"/>
          </a>
        </td>
      </tr>
    </table>
    
    <p>
      Originally taken on <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(/formresult/selected/exifData/dateTime), 'D, d M H:i')"/>
      with <xsl:value-of select="/formresult/selected/exifData/make"/>'s
      <xsl:value-of select="/formresult/selected/exifData/model"/>.

      (<small>
      <xsl:if test="/formresult/selected/exifData/apertureFNumber != ''">
        <xsl:value-of select="/formresult/selected/exifData/apertureFNumber"/>
      </xsl:if>
      <xsl:if test="/formresult/selected/exifData/exposureTime != ''">
        <xsl:text>, </xsl:text>
        <xsl:value-of select="/formresult/selected/exifData/exposureTime"/> sec.
      </xsl:if>  
      <xsl:if test="/formresult/selected/exifData/isoSpeedRatings != ''">
        <xsl:text>, ISO </xsl:text>
        <xsl:value-of select="/formresult/selected/exifData/isoSpeedRatings"/>
      </xsl:if>  
      <xsl:if test="/formresult/selected/exifData/focalLength != '0'">
        <xsl:text>, focal length: </xsl:text>
        <xsl:value-of select="/formresult/selected/exifData/focalLength"/>
        <xsl:text> mm</xsl:text>
      </xsl:if>
      <xsl:if test="(/formresult/selected/exifData/flash mod 8) = 1">
        <xsl:text>, flash fired</xsl:text>
      </xsl:if>
      </small>)
    </p>
  </xsl:template>
  
</xsl:stylesheet>
