<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
 !
 ! $Id: master.xsl 4410 2004-12-18 18:19:28Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="layout.inc.xsl"/>
  <xsl:include href="news.inc.xsl"/>

  <xsl:template name="html-head">
    <link rel="shortcut icon" href="/common/favicon.ico" />
    <link rel="alternate" type="application/rss+xml" title="RSS Feed for XP Framework news" href="/rss/"/>
  </xsl:template>
 
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <xsl:apply-templates select="/formresult/entry"/>
         </td>
        <td id="context">
          <xsl:call-template name="context"/>
        </td>
       </tr>
     </table>
  </xsl:template>
  
  <xsl:template name="html-title">
    <xsl:value-of select="/formresult/entry/title"/> - XP Framework News
  </xsl:template>
  
  <xsl:template match="entry">
    <h2><a href="{xp:link('overview')}"><xsl:value-of select="title"/></a></h2>
    <em>
      at <xsl:value-of select="xp:date(date)"/>
      in <xsl:for-each select="categories/category">
        <a href="{xp:link(concat('bycategory?', @id))}">
          <xsl:value-of select="@name"/><xsl:if test="position() != last()">, </xsl:if>
        </a>
      </xsl:for-each>
      by <xsl:value-of select="author"/> 
      (<xsl:value-of select="num_comments"/> comments)
    </em>
    <p><xsl:apply-templates select="body"/></p>
    <p><xsl:apply-templates select="extended"/></p>
    <br/><br clear="all"/>
    
    <xsl:for-each select="comments/comment">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
  </xsl:template>
  
  <xsl:template match="comment">
    <!-- XXX TBI -->
  </xsl:template>
  
  <xsl:template name="context">
    <h3>
      <img align="right" src="/common/image/feed.png"/>
      Subscribe
    </h3>
    You can subscribe to the XP framework's news by using <a href="/rss/">RSS syndication</a>.
    <br clear="all"/>
  </xsl:template>
</xsl:stylesheet>