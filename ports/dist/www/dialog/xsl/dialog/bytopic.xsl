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

  <!--
   ! Template for pager
   !
   ! @purpose  Links to previous and next
   !-->
  <xsl:template name="pager">
    <center>
      <a title="Newer entries" class="pager{/formresult/pager/@offset &gt; 0}" id="previous">
        <xsl:if test="/formresult/pager/@offset &gt; 0">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat('bytopic?page', /formresult/pager/@offset - 1))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Older entries" class="pager{(/formresult/pager/@offset + 1) * /formresult/pager/@perpage &lt; /formresult/pager/@total}" id="next">
        <xsl:if test="(/formresult/pager/@offset + 1) * /formresult/pager/@perpage &lt; /formresult/pager/@total">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat('bytopic?page', /formresult/pager/@offset + 1))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.Album']">
    <img width="150" height="113" border="0" src="/albums/{@origin-name}/thumb.{name}"/>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.EntryCollection']">
    <img width="150" height="113" border="0" src="/albums/{@origin-name}/thumb.{name}"/>
  </xsl:template>

  <xsl:template match="image[@origin-class = 'de.thekid.dialog.SingleShot']">
    <img width="150" height="113" border="0" src="/shots/thumb.color.{name}"/>
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
    <a title="Album of {@num_images} images in {@num_chapters} chapters" href="{func:linkAlbum(@name)}">
      <xsl:value-of select="@title"/>
    </a>
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
    <a title="Featured image" href="{func:linkShot(@name, 0)}">
      <xsl:value-of select="@title"/>
    </a>
  </xsl:template>

  <!--
   ! Template for collections 
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.EntryCollection']">
    <a title="Collection of {@num_entries}" href="{func:linkCollection(@name)}">
      <xsl:value-of select="@title"/>
    </a>
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
      <xsl:if test="/formresult/pager/@offset &gt; 0">
        &#xbb;
        <a href="{func:linkPage(/formresult/pager/@offset)}">
          Page #<xsl:value-of select="/formresult/pager/@offset"/>
        </a>
      </xsl:if>
    </h3>
    <br clear="all"/>

    <xsl:call-template name="pager"/>
    
    <xsl:for-each select="/formresult/topics/topic">
      <h2><a href="{func:link(concat('topic?', @name))}"><xsl:value-of select="@title"/></a></h2>
      <table class="highlights" border="0">
        <tr>
          <xsl:copy-of select="func:highlights(exsl:node-set(featured/image))"/>
        </tr>
      </table>
      <table class="bydate_list" border="0" width="770">
        <xsl:for-each select="origins/year">
          <tr>
            <td id="day" valign="top">
              <h2><xsl:value-of select="@num"/></h2>
            </td>
            <td id="content" valign="top">
              <xsl:for-each select="entry">
                <xsl:apply-templates select="."/>
                <xsl:if test="position() &lt; last()">, </xsl:if>
              </xsl:for-each>
            </td>
          </tr>
        </xsl:for-each>
      </table>

      <br/><br clear="all"/>
    </xsl:for-each>

    <xsl:call-template name="pager"/>
    <br clear="all"/>
  </xsl:template>
  
</xsl:stylesheet>
