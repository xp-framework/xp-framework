<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC list include
 !
 ! $Id$
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

  <xsl:template name="pager">
    <xsl:param name="link"/>
    <xsl:param name="count"/>
    <xsl:param name="page"/>
    <xsl:param name="perpage" select="10"/>
  
    <div class="pager">
      <a title="Newer entries" class="pager{$page &gt; 0}" id="previous">
        <xsl:if test="$page &gt; 0">
          <xsl:attribute name="href"><xsl:value-of select="concat($link, ',page', $page - 1)"/></xsl:attribute>
        </xsl:if>
        &#xab;
      </a>
      <a title="Older entries" class="pager{($page + 1) * $perpage &lt; $count}" id="next">
        <xsl:if test="($page + 1) * $perpage &lt; $count">
          <xsl:attribute name="href"><xsl:value-of select="concat($link, ',page', $page + 1)"/></xsl:attribute>
        </xsl:if>
        &#xbb;
      </a>
    </div>
  </xsl:template>

  <xsl:template name="list">
    <xsl:param name="elements"/>

    <xsl:call-template name="pager">
      <xsl:with-param name="link" select="xp:link(concat('rfc/list?', exsl:node-set($elements)/@criteria, '.', exsl:node-set($elements)/@filter))"/>
      <xsl:with-param name="count" select="exsl:node-set($elements)/@count"/>
      <xsl:with-param name="page" select="exsl:node-set($elements)/@page"/>
    </xsl:call-template>
    
    <xsl:for-each select="exsl:node-set($elements)/rfc">
      <h2>
        <img src="/image/{status/@id}.png" widht="16" height="16"/>
        <a href="{xp:link(concat('rfc/view?', @number))}">
          #<xsl:value-of select="@number"/>: <xsl:value-of select="title"/>
        </a>
      </h2>
      <em>
        Created <xsl:value-of select="created"/> by <acronym title="{author/realname}"><xsl:value-of select="author/cn"/></acronym>
        <xsl:if test="status != ''">
          <xsl:text>, </xsl:text>
          <b><xsl:value-of select="status"/></b>
        </xsl:if>
      </em>
      <br/><br clear="all"/>
      <xsl:apply-templates select="content/p[2]"/>
      <br clear="all"/>
    </xsl:for-each>

    <xsl:call-template name="pager">
      <xsl:with-param name="link" select="xp:link(concat('rfc/list?', exsl:node-set($elements)/@criteria, '.', exsl:node-set($elements)/@filter))"/>
      <xsl:with-param name="count" select="exsl:node-set($elements)/@count"/>
      <xsl:with-param name="page" select="exsl:node-set($elements)/@page"/>
    </xsl:call-template>
  </xsl:template>

</xsl:stylesheet>
