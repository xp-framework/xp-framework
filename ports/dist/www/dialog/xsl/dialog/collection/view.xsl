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
      'Collection: ', 
      /formresult/collection/@title, ' @ ', 
      /formresult/config/title
    )"/>
  </xsl:template>
  
  <!--
   ! Template for albums
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.Album']">
    <h2>
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(created/value), 'd M')"/>:
      <a href="{func:linkAlbum(@name)}">
        <xsl:value-of select="@title"/>
      </a>
    </h2>
    <p align="justify">
      <xsl:copy-of select="description"/>
      <br clear="all"/>
    </p>

    <h4>Highlights</h4>
    <table class="highlights" border="0">
      <tr>
        <xsl:for-each select="highlights/highlight">
          <td>
            <a href="{func:linkImage(../../@name, 0, 'h', position()- 1)}">
              <img width="150" height="113" border="0" src="/albums/{../../@name}/thumb.{name}"/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
    </table>
    <p>
      This album contains <xsl:value-of select="@num_images"/> images in <xsl:value-of select="@num_chapters"/> chapters -
      <a href="{func:linkAlbum(@name)}">See more</a>
    </p>
    <br/><br clear="all"/>
  </xsl:template>
  
  <!--
   ! Template for updates
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.Update']">
    <div class="datebox">
      <h2><xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(date/value), 'd')"/></h2> 
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(date/value), 'M Y')"/>
    </div>
    <div class="datebox">
      <h2><xsl:value-of select="date/mday"/></h2> 
      <xsl:value-of select="substring(date/month, 1, 3)"/>&#160;
      <xsl:value-of select="date/year"/>
    </div>
    <h2>
      Updated: <xsl:value-of select="@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="description"/>
      - <a href="{func:linkAlbum(@album)}">Go to album</a>
      <br clear="all"/>
    </p>
    <br/><br clear="all"/>
  </xsl:template>

  <!--
   ! Template for updates
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.SingleShot']">
    <div class="datebox">
      <h2><xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(date/value), 'd')"/></h2> 
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(date/value), 'M Y')"/>
    </div>
    <h2>
      Featured image: <xsl:value-of select="@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="description"/>
      <br clear="all"/>
    </p>
    <table border="0">
      <tr>
        <td rowspan="2">
          <img class="singleshot" border="0" src="/shots/detail.{@filename}" width="459" height="230"/>
        </td>
        <td valign="top">
          <a href="{func:linkShot(@name, 0)}">
            <img class="singleshot_thumb" border="0" src="/shots/thumb.color.{@filename}" width="150" height="113"/>
          </a>
        </td>
      </tr>
      <tr>
        <td valign="bottom">
          <a href="{func:linkShot(@name, 1)}">
            <img class="singleshot_thumb" border="0" src="/shots/thumb.gray.{@filename}" width="150" height="113"/>
          </a>
        </td>
      </tr>
    </table>
    <br/><br clear="all"/>
  </xsl:template>

  <!--
   ! Template for collections 
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.EntryCollection']">
    <div class="datebox">
      <h2><xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(created/value), 'd')"/></h2> 
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(created/value), 'M Y')"/>
    </div>
    <h2>
      Collection: <xsl:value-of select="@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="description"/>
      <br clear="all"/>
    </p>

    <h4>Albums</h4>
    <table class="collection_list" border="0">
      <xsl:for-each select="entry[@type='de.thekid.dialog.Album']">
        <tr>
          <td width="160" valign="top">
            <img width="150" height="113" border="0" src="/albums/{@name}/thumb.{./highlights/highlight[1]/name}"/>
          </td>
          <td width="466" valign="top">
            <h3>
              <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(created/value), 'd M')"/>:
              <a href="{func:linkAlbum(@name)}">
                <xsl:value-of select="@title"/>
              </a>
              (<xsl:value-of select="@num_images"/> images in <xsl:value-of select="@num_chapters"/> chapters)
            </h3>
            <p align="justify">
              <xsl:copy-of select="description"/>
              <br clear="all"/>
            </p>
          </td>
        </tr>
      </xsl:for-each>
    </table>
      
    <br/><br clear="all"/>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h3>
      <a href="{func:linkPage(0)}">Home</a>
      <xsl:if test="/formresult/collection/@page &gt; 0">
        &#xbb;
        <a href="{func:linkPage(/formresult/collection/@page)}">
          Page #<xsl:value-of select="/formresult/collection/@page"/>
        </a>
      </xsl:if>
      &#xbb;
      <a href="{func:linkCollection(/formresult/collection/@name)}">
        <xsl:value-of select="/formresult/collection/@title"/> Collection
      </a>
    </h3>
    <br clear="all"/>

    <div class="datebox">
      <h2><xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(/formresult/collection/created/value), 'd')"/></h2> 
      <xsl:value-of select="php:function('XSLCallback::invoke', 'xp.date', 'format', string(/formresult/collection/created/value), 'M Y')"/>
    </div>
    <h2>
      Collection: <xsl:value-of select="/formresult/collection/@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="/formresult/collection/description"/>
    </p>
    <br/><br clear="all"/>

    <xsl:for-each select="/formresult/entries/entry">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
    
    <br clear="all"/>
  </xsl:template>
  
</xsl:stylesheet>
