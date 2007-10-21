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
    <xsl:value-of select="concat(
      'Chapter #', /formresult/chapter/@id, ' of ',
      /formresult/album/@title, ' @ ', 
      /formresult/config/title
    )"/>
  </xsl:template>

  <!--
   ! Function that draws the images of a chapter
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <func:function name="func:chapter-images">
    <xsl:param name="album"/>
    <xsl:param name="chapter"/>
    <xsl:param name="images"/>
    <xsl:param name="i" select="1"/>
    <xsl:param name="max" select="5"/>
    
    <func:result>
      <tr>
        <xsl:for-each select="exsl:node-set($images)/image[position() &gt;= $i and position() &lt; $i + $max]">
          <td>
            <a href="{func:linkImage($album, $chapter, 'i', $i - 2 + position())}">
              <img width="150" height="113" border="0" src="/albums/{$album}/thumb.{name}"/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
      <xsl:if test="$i &lt; count(exsl:node-set($images)/image)">
        <xsl:copy-of select="func:chapter-images($album, $chapter, exsl:node-set($images), $i + $max)"/>
      </xsl:if>
    </func:result>  
  </func:function>
  
  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
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
      <a href="{func:linkChapter(/formresult/album/@name, /formresult/chapter/@id - 1)}">
        Chapter #<xsl:value-of select="/formresult/chapter/@id"/>
      </a>
    </h3>
    <br clear="all"/>

    <xsl:variable name="total" select="count(/formresult/chapter/images/image)"/>
    <xsl:variable name="oldest" select="/formresult/chapter/images/image[1]"/>

    <div class="datebox">
      <h2><xsl:value-of select="/formresult/chapter/@id"/></h2> 
    </div>
    <h2>
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(exsl:node-set($oldest)/exifData/dateTime/value), 'D, d M H:00')"/>
    </h2>
    <p>
      This chapter contains
      <xsl:choose>
        <xsl:when test="$total = 1">1 image</xsl:when>
        <xsl:otherwise><xsl:value-of select="$total"/> images</xsl:otherwise>
      </xsl:choose>
    </p>

    <center>
      <a title="Previous image" class="pager{/formresult/chapter/@previous != ''}" id="previous">
        <xsl:if test="/formresult/chapter/@previous != ''">
          <xsl:attribute name="href"><xsl:value-of select="func:linkChapter(
            /formresult/album/@name, 
            /formresult/chapter/@previous
          )"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Next image" class="pager{/formresult/chapter/@next != ''}" id="next">
        <xsl:if test="/formresult/chapter/@next != ''">
          <xsl:attribute name="href"><xsl:value-of select="func:linkChapter(
            /formresult/album/@name,
            /formresult/chapter/@next
          )"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>

    <table border="0" class="chapter">
      <xsl:copy-of select="func:chapter-images(
        /formresult/album/@name, 
        /formresult/chapter/@id - 1,
        /formresult/chapter/images
      )"/>
    </table>
  </xsl:template>
  
</xsl:stylesheet>
