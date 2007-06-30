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
 extension-element-prefixes="func"
>
  <xsl:include href="layout.xsl"/>
  
  <!--
   ! Template for pager
   !
   ! @purpose  Links to previous and next
   !-->
  <xsl:template name="pager">
    <center>
      <a title="Newer entries" class="pager{/formresult/pager/@offset &gt; 0}" id="previous">
        <xsl:if test="/formresult/pager/@offset &gt; 0">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'static?page', 
            /formresult/pager/@offset - 1
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xab;" src="/image/prev.gif" border="0" width="19" height="15"/>
      </a>
      <a title="Older entries" class="pager{(/formresult/pager/@offset + 1) * /formresult/pager/@perpage &lt; /formresult/pager/@total}" id="next">
        <xsl:if test="(/formresult/pager/@offset + 1) * /formresult/pager/@perpage &lt; /formresult/pager/@total">
          <xsl:attribute name="href"><xsl:value-of select="func:link(concat(
            'static?page', 
            /formresult/pager/@offset + 1
          ))"/></xsl:attribute>
        </xsl:if>
        <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
      </a>
    </center>
  </xsl:template>
  
  <!--
   ! Template for albums
   !
   ! @purpose  Specialized entry template
   !-->
  <xsl:template match="entry[@type = 'de.thekid.dialog.Album']">
    <div class="datebox">
      <h2><xsl:value-of select="created/mday"/></h2> 
      <xsl:value-of select="substring(created/month, 1, 3)"/>&#160;
      <xsl:value-of select="created/year"/>
    </div>
    <h2>
      <a href="{func:link(concat('album/view?', @name))}">
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
            <a href="{func:link(concat('image/view?', ../../@name, ',h,0,', position()- 1))}">
              <img width="150" height="113" border="0" src="/albums/{../../@name}/thumb.{name}"/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
    </table>
    <p>
      This album contains <xsl:value-of select="@num_images"/> images in <xsl:value-of select="@num_chapters"/> chapters -
      <a href="{func:link(concat('album/view?', @name))}">See more</a>
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
      <h2><xsl:value-of select="date/mday"/></h2> 
      <xsl:value-of select="substring(date/month, 1, 3)"/>&#160;
      <xsl:value-of select="date/year"/>
    </div>
    <h2>
      Updated: <xsl:value-of select="@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="description"/>
      - <a href="{func:link(concat('album/view?', @album))}">Go to album</a>
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
      <h2><xsl:value-of select="date/mday"/></h2> 
      <xsl:value-of select="substring(date/month, 1, 3)"/>&#160;
      <xsl:value-of select="date/year"/>
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
        <td rowspan="3">
          <img class="singleshot" border="0" src="/shots/detail.{@filename}" width="619" height="347"/>
        </td>
        <td valign="top">
          <a href="{func:link(concat('shot/view?', @name, ',0'))}">
            <img class="singleshot_thumb" border="0" src="/shots/thumb.color.{@filename}" width="150" height="113"/>
          </a>
        </td>
      </tr>
      <tr>
        <td valign="top">
          <a href="{func:link(concat('shot/view?', @name, ',1'))}">
            <img class="singleshot_thumb" border="0" src="/shots/thumb.gray.{@filename}" width="150" height="113"/>
          </a>
        </td>
      </tr>
      <tr>
        <td valign="bottom">
          <img src="/image/blank.gif" width="150" height="113"/>
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
      <h2><xsl:value-of select="created/mday"/></h2>
      <xsl:value-of select="substring(created/month, 1, 3)"/>&#160;
      <xsl:value-of select="created/year"/>
    </div>
    <h2>
      <a href="{func:link(concat('collection/view?', @name))}">
        Collection: <xsl:value-of select="@title"/>
      </a>
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
              <xsl:value-of select="concat(created/mday, ' ', substring(created/month, 1, 3))"/>:
              <a href="{func:link(concat('album/view?', @name))}">
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
      <a href="{func:link('static')}">Home</a>
      <xsl:if test="/formresult/pager/@offset &gt; 0">
        &#xbb;
        <a href="{func:link(concat('static?page', /formresult/pager/@offset))}">
          Page #<xsl:value-of select="/formresult/pager/@offset"/>
        </a>
      </xsl:if>
    </h3>
    <br clear="all"/>
    <xsl:call-template name="pager"/>

    <xsl:for-each select="/formresult/entries/entry">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
    
    <br clear="all"/>
    <xsl:call-template name="pager"/>
  </xsl:template>
  
</xsl:stylesheet>
