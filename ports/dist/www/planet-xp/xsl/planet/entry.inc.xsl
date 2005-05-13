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

  <!--
   ! Send links thorugh dereferer
   !
   !-->
  <xsl:template match="a">
    <a href="/deref/?{@href}"><xsl:apply-templates select="./*|text()"/></a>
  </xsl:template>
  
  <xsl:template match="br">
    <br/>
  </xsl:template>
  
  <!--
   ! Allowable attributes for img
   !
   !-->
  <xsl:template match="img/@width|img/@height|img/@title|img/@alt|img/@border|img/@hspace|img/@align">
    <xsl:copy-of select="."/>
  </xsl:template>
  
  <!--
   ! Template for img. Maybe we want to redirect redirect
   ! inline images through a proxy?
   !
   !-->
  <xsl:template match="img">
    <xsl:choose>
      <xsl:when test="starts-with(@src, 'http://') or starts-with(@src, 'https://')">
        <img src="{@src}">
          <xsl:apply-templates select="@*"/>
        </img>
      </xsl:when>
      <xsl:otherwise>
        [Image cannot be displayed properly! Url: <xsl:value-of select="@src"/>]
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <!--
   ! Whitelist of allowed HTML standard tags.
   !
   !-->
  <xsl:template match="pre|b|strong|code|ul|li|ol|tt|blockquote|quote|p">
    <xsl:copy select="node()">
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <!--
   ! Allowable attributes for div
   !
   !-->
  <xsl:template match="div/@align">
    <xsl:copy-of select="."/>
  </xsl:template>
  
  <!-- 
   ! Template for div
   !
   !-->
  <xsl:template match="div">
    <div>
      <xsl:apply-templates select="@*"/>
      <xsl:apply-templates/>
    </div>
  </xsl:template>

  <!--
   ! Intermediate template to get rid of <content>
   !
   !-->
  <xsl:template match="content">
    <xsl:apply-templates/>
  </xsl:template>
  
  <!--
   ! Default template: matches everything unmatched and displays
   ! "raw" html. This way, we can see missed tags...
   !-->
  <xsl:template match="*">
    &lt;<xsl:value-of select="name()"/>&gt;
    <xsl:apply-templates/>
    &lt;/<xsl:value-of select="name()"/>&gt;
  </xsl:template>
  
  <xsl:template name="display-entry">
    <xsl:param name="entry"/>
    
    <div class="entry entryclass-{feed/@feed_id}">
      <h3>
        <a href="/deref/?{@link}" title="{feed/@title}: {feed/@description}">
          <xsl:value-of select="./@title"/>
        </a>
      </h3>
      <p>
        <xsl:apply-templates select="content"/>
      </p>
      <em>
        <xsl:if test="string-length(@author) != 0">
          Written by <xsl:value-of select="./@author"/>
        </xsl:if>
        <xsl:if test="string-length(@author) = 0">Posted</xsl:if>

        at <xsl:value-of select="func:time(published)"/>

        <xsl:if test="string-length(feed/@title) &gt; 0"> in 
          <a href="/deref/?{feed/@link}"><xsl:value-of select="feed/@title"/></a>
        </xsl:if>
      </em>
    </div>
    <br clear="all"/>
  </xsl:template>
</xsl:stylesheet>
