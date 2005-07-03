<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: news.xsl 4958 2005-04-09 19:58:22Z kiesel $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="../layout.xsl"/>
  <xsl:include href="../../news.inc.xsl"/>
  <xsl:include href="../../date.inc.xsl"/>
  <xsl:include href="../../wizard.inc.xsl"/>
  
  <xsl:template name="context">
    <xsl:if test="func:hasPermission('create_news') != ''">
      <table class="sidebar" cellpadding="0" cellspacing="0" width="170">
        <tr><td class="sidebar_head">Aktionen</td></tr>
        <tr><td><a href="http://cms.uska.de">Artikel-Editor öffnen</a></td></tr>
      </table>
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="content">
    <h1>
      <a href="{func:link('news')}">News</a> &#xbb; Eintrag #<xsl:value-of select="/formresult/entry/@id"/>
    </h1>
    <div class="entry">
      <h3>
        <a href="{func:link(concat('news/view?', /formresult/entry/@id))}">
          <xsl:value-of select="/formresult/entry/title"/>
        </a>
      </h3>
      
      <!-- Check for errors -->
      <xsl:if test="/formresult/formerrors/error">
        <xsl:for-each select="/formresult/formerrors/error">
          <xsl:variable name="errtxt">
            <xsl:value-of select="func:get_text(concat('error#', @checker, '-', @type))"/><br/>
            <xsl:value-of select="."/>
          </xsl:variable>
          <xsl:copy-of select="func:box('error', $errtxt)"/>
        </xsl:for-each>
      </xsl:if>
      
      <p>
        <xsl:apply-templates select="/formresult/entry/body"/>
      </p>
      <p>
        <xsl:apply-templates select="/formresult/entry/extended"/>
      </p>
      <em>
        Geschrieben von <xsl:value-of select="/formresult/entry/author"/> 
        am <xsl:value-of select="func:datetime(/formresult/entry/date)"/>
      </em>
      
      <xsl:if test="count(/formresult/entry/comments/comment) &gt; 0">
        <h4>Kommentare</h4>
        <xsl:for-each select="/formresult/entry/comments/comment">
          <div class="comment">
            <p>
              <xsl:apply-templates select="body"/>
            </p>
            <small>
              Von <xsl:value-of select="author"/>
              am <xsl:value-of select="func:datetime(date)"/> 
            </small>
          </div>
          <br clear="all"/>
        </xsl:for-each>
      </xsl:if>
    </div>
  </xsl:template>
</xsl:stylesheet>
