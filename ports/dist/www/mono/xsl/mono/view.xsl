<?xml version="1.0" encoding="utf-8"?>
<!-- 
 ! View pictures
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

  <xsl:template name="contents">
    <div id="container">
      <div id="header">
        <!-- ... -->
        <div id="header-nav">
          <a href="{func:link('view')}">today</a> |
          <a href="{func:link('about')}">about</a> |
          <a href="{func:link('links')}">links</a>
        </div>
      </div>
      
      <div id="picture-frame">
        <xsl:call-template name="view-prev-next"/>
        <div id="picture-inner-frame">
          <xsl:choose>
            <xsl:when test="/formresult/navigation/@previous-id != ''">
              <a href="{func:link(concat('view?', /formresult/navigation/@previous-id))}">
              </a>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="view-picture"/>
            </xsl:otherwise>
          </xsl:choose>
        </div>
        <p id="picture-description">
          <xsl:if test="not(/formresult/description)">
            There is no description for this picture
          </xsl:if>
          
          <xsl:if test="/formresult/description">
            <xsl:copy-of 
             select="/formresult/description"
             disable-output-escaping="yes"
            />
          </xsl:if>
        </p>
        <xsl:call-template name="view-prev-next"/>
      </div>
    </div>
  </xsl:template>
  
  <xsl:template name="view-picture">
    <img
     id="picture"
     src="/shots/{/formresult/navigation/@currentid}/{/formresult/picture/filename}"
     width="{/formresult/picture/width}"
     height="{/formresult/picture/height}"
     title="..."
    />
  </xsl:template>
  
  <xsl:template name="view-prev-next">
    <!-- Link to previous picture -->
    <div id="prev-next">
      <xsl:if test="/formresult/navigation/@previous-id != ''">
        <a href="{func:link(concat('view?', /formresult/navigation/@previous-id))}">
          &lt;&lt; day before
        </a>
      </xsl:if>
      <!-- Link to next picture -->
      <xsl:if test="/formresult/navigation/@next-id != ''">
        <a href="{func:link(concat('view?', /formresult/navigation/@next-id))}">
         | day after &gt;&gt;
        </a>
      </xsl:if>
    </div>
  </xsl:template>
</xsl:stylesheet>
