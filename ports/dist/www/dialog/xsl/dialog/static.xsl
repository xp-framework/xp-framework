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
   ! Template for content
   !
   ! @see      ../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
  
    <xsl:for-each select="/formresult/albums/album">
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
                <img border="0" src="/albums/{../../@name}/thumb.{name}"/>
              </a>
            </td>
          </xsl:for-each>
        </tr>
      </table>
      <p><a href="{func:link(concat('album/view?', @name))}">See more</a></p>
      <hr/>
    </xsl:for-each>
    
    <br clear="all"/> 
    <table width="100%">
      <tr>
        <td align="left">
          <xsl:if test="/formresult/pager/@offset &gt; 0">
            <a href="{func:link(concat(
              'static?page', 
              /formresult/pager/@offset - 1
            ))}">NEXT</a>
          </xsl:if>
        </td>
        <td align="right">
          <xsl:if test="(/formresult/pager/@offset + 1) * /formresult/pager/@perpage &lt; /formresult/pager/@total">
            <a href="{func:link(concat(
              'static?page', 
              /formresult/pager/@offset + 1
            ))}">PREV</a>
          </xsl:if>
        </td>
      </tr>
    </table>
  </xsl:template>
  
</xsl:stylesheet>
