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
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h3>
      <a href="{func:link('static')}">Home</a> &#xbb; 
      
      <xsl:if test="/formresult/album/@page &gt; 0">
        <a href="{func:link(concat('static?page', /formresult/album/@page))}">
          Page #<xsl:value-of select="/formresult/album/@page"/>
        </a>
        &#xbb;
      </xsl:if>
     
      <xsl:if test="/formresult/album/collection">
        <a href="{func:link(concat('collection/view?', /formresult/album/collection/@name))}">
          <xsl:value-of select="/formresult/album/collection/@title"/> Collection
        </a>
         &#xbb;
      </xsl:if>
      
      <a href="{func:link(concat('album/view?', /formresult/album/@name))}">
        <xsl:value-of select="/formresult/album/@title"/>
      </a>
    </h3>
    <br clear="all"/>

    <div class="datebox">
      <h2><xsl:value-of select="/formresult/album/created/mday"/></h2> 
      <xsl:value-of select="substring(/formresult/album/created/month, 1, 3)"/>&#160;
      <xsl:value-of select="/formresult/album/created/year"/>
    </div>
    <h2>
      <xsl:value-of select="/formresult/album/@title"/>
    </h2>
    <p align="justify">
      <xsl:copy-of select="/formresult/album/description"/>
      <br clear="all"/>
    </p>
    
    <h4>Highlights</h4>
    <table class="highlights" border="0">
      <tr>
        <xsl:for-each select="/formresult/album/highlights/highlight">
          <td>
            <a href="{func:link(concat('image/view?', ../../@name, ',h,0,', position()- 1))}">
              <img width="150" height="113" border="0" src="/albums/{../../@name}/thumb.{name}"/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
    </table>
    <p>
      This album contains <xsl:value-of select="/formresult/album/@num_images"/> images in <xsl:value-of select="/formresult/album/@num_chapters"/> chapters.
    </p>
    <br clear="all"/>

    <xsl:for-each select="/formresult/album/chapters/chapter">
      <xsl:variable name="total" select="count(images/image)"/>
      <xsl:variable name="oldest" select="images/image[1]"/>
      <xsl:variable name="newest" select="images/image[$total]"/>
      <xsl:variable name="chapter" select="position() - 1"/>

      <div class="datebox">
        <h2><xsl:value-of select="position()"/></h2> 
      </div>
      <h2>
        <a href="{func:link(concat('chapter/view?', ../../@name, ',', $chapter))}">
          <xsl:value-of select="concat(
            exsl:node-set($oldest)/exifData/dateTime/weekday, ', ',
            exsl:node-set($oldest)/exifData/dateTime/mday, ' ',
            exsl:node-set($oldest)/exifData/dateTime/month, ' ',
            exsl:node-set($oldest)/exifData/dateTime/hours, ':00'
          )"/>
        </a>
      </h2>
      <p>
        This chapter contains
        <xsl:choose>
          <xsl:when test="$total = 1">1 image</xsl:when>
          <xsl:otherwise><xsl:value-of select="$total"/> images</xsl:otherwise>
        </xsl:choose>
      </p>

      <table border="0" class="chapter">
        <tr>
          <xsl:for-each select="images/image[position() &lt; 6]">
            <td>
              <a href="{func:link(concat('image/view?', ../../../../@name, ',i,', $chapter, ',', position()- 1))}">
                <img width="150" height="113" border="0" src="/albums/{../../../../@name}/thumb.{name}"/>
              </a>
            </td>
          </xsl:for-each>
          <xsl:if test="$total &gt; 5">
            <td>
              <a 
               title="Show image #6" 
               class="pager" 
               id="true"
               href="{func:link(concat('image/view?', /formresult/album/@name, ',i,', $chapter, ',', 5))}"
              >
                <img alt="&#xbb;" src="/image/next.gif" border="0" width="19" height="15"/>
              </a>
            </td>
          </xsl:if>
        </tr>
      </table>
      <br/><br clear="all"/>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
