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
  <xsl:import href="layout.xsl"/>
  
  <!--
   ! Template for page title
   !
   ! @see       ../layout.xsl
   !-->
  <xsl:template name="page-title">
    <xsl:text>By Topic</xsl:text>       
    <xsl:text> @ </xsl:text>
    <xsl:value-of select="/formresult/config/title"/>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.Album']">
    <a href="{func:linkImage(@origin-name, @origin-chapter, @origin-type, @origin-id)}">
      <img width="150" height="113" border="0" src="/albums/{@origin-name}/thumb.{@name}"/>
    </a>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.EntryCollection']">
    <a href="{func:linkImage(@origin-name, @origin-chapter, @origin-type, @origin-id)}">
      <img width="150" height="113" border="0" src="/albums/{@origin-name}/thumb.{@name}"/>
    </a>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.SingleShot']">
    <a href="{func:linkShot(@origin-name, @origin-id)}">
      <img width="150" height="113" border="0" src="/shots/thumb.color.{@name}"/>
    </a>
  </xsl:template>

  <!--
   ! Function that draws the highlights
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <func:function name="func:highlights">
    <xsl:param name="entries"/>
    <xsl:param name="i" select="1"/>
    <xsl:param name="max" select="5"/>
    
    <func:result>
      <tr>
        <xsl:for-each select="exsl:node-set($entries)[position() &gt;= $i and position() &lt; $i + $max]">
          <td>
            <xsl:apply-templates select="."/>
          </td>
        </xsl:for-each>
      </tr>
      <xsl:if test="$i &lt; count(exsl:node-set($entries))">
        <xsl:copy-of select="func:highlights(exsl:node-set($entries), $i + $max)"/>
      </xsl:if>
    </func:result>  
  </func:function>

  <!--
   ! Template for albums
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.Album']">
    <h2>
      Originally from: <a href="{func:linkAlbum(@name)}"><xsl:value-of select="@title"/></a>
    </h2>
    <p>
      <xsl:variable name="total" select="count(image)"/>
      <xsl:choose>
        <xsl:when test="$total = 1">One image</xsl:when>
        <xsl:otherwise><xsl:value-of select="$total"/> images</xsl:otherwise>
      </xsl:choose>
      from this album of <xsl:value-of select="@num_images"/> images in <xsl:value-of select="@num_chapters"/> chapters.
    </p>
  </xsl:template>
  
  <!--
   ! Template for updates
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.Update']">
    <a title="Update" href="{func:linkAlbum(@album)}">
      <xsl:value-of select="@title"/>
    </a>
  </xsl:template>

  <!--
   ! Template for updates
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.SingleShot']">
    <h2>
      Originally from: <a href="{func:linkShot(@name, 0)}"><xsl:value-of select="@title"/></a>
    </h2>
    <p>
      A featured image.
    </p>
  </xsl:template>

  <!--
   ! Template for collections 
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.EntryCollection']">
    <h2>
      Originally from: <a href="{func:linkCollection(@name)}"><xsl:value-of select="@title"/></a>
    </h2>
    <p>
      <xsl:variable name="total" select="count(image)"/>
      <xsl:choose>
        <xsl:when test="$total = 1">One image</xsl:when>
        <xsl:otherwise><xsl:value-of select="$total"/> images</xsl:otherwise>
      </xsl:choose>
      from this collection of <xsl:value-of select="@num_entries"/> images in <xsl:value-of select="@num_chapters"/> chapters.
    </p>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h3>
      <a href="/">Home</a>
      &#xbb;
      <a href="{func:link('bytopic')}">
        By Topic
      </a>
      &#xbb;
      <a href="{func:link(concat('topic?', /formresult/topic/@name))}">
        <xsl:value-of select="/formresult/topic/@title"/>
      </a>
    </h3>
    <br clear="all"/>

    <xsl:for-each select="/formresult/topic/origins/entry">
      <div class="datebox">
        <h2><xsl:value-of select="position()"/></h2> 
      </div>
      <xsl:apply-templates select="."/>
      <table class="highlights" border="0">
        <tr>
          <xsl:copy-of select="func:highlights(exsl:node-set(image))"/>
        </tr>
      </table>
      <br clear="all"/>
    </xsl:for-each>
    
  </xsl:template>
  
</xsl:stylesheet>
