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
    <h2>
      <a href="{func:link('static')}">Home</a> &#xbb; 
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
              <img border="0" src="/albums/{../../@name}/thumb.{name}"/>
            </a>
          </td>
        </xsl:for-each>
      </tr>
    </table>
    <hr/>

    <xsl:for-each select="/formresult/album/chapters/chapter">
      <xsl:variable name="total" select="count(images/image)"/>
      <xsl:variable name="oldest" select="images/image[1]"/>
      <xsl:variable name="newest" select="images/image[$total]"/>
      <xsl:variable name="chapter" select="position() - 1"/>
      <h4>
        <xsl:value-of select="func:datetime(exsl:node-set($oldest)/exifData/dateTime)"/> -
        <xsl:value-of select="func:datetime(exsl:node-set($newest)/exifData/dateTime)"/>
        (
        <xsl:choose>
          <xsl:when test="$total = 1">1 image</xsl:when>
          <xsl:otherwise><xsl:value-of select="$total"/> images</xsl:otherwise>
        </xsl:choose>
        )
      </h4>
      <table class="highlights" border="0">
        <tr>
          <xsl:for-each select="images/image[position() &lt; 5]">
            <td>
              <a href="{func:link(concat('image/view?', ../../../../@name, ',i,', $chapter, ',', position()- 1))}">
                <img border="0" src="/albums/{../../../../@name}/thumb.{name}"/>
              </a>
            </td>
          </xsl:for-each>
        </tr>
      </table>
      <xsl:if test="$total &gt; 4">
        <p><a href="{func:link(concat('chapter/view?', ../../@name, ',', $chapter))}">See more</a></p>
      </xsl:if>
      <hr/>
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>
