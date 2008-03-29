<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Shows apidoc overview
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:str="http://exslt.org/strings"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>
  <xsl:include href="api/doc.inc.xsl"/>

  <xsl:template match="overview">
    <div id="breadcrumb">
      <a href="{xp:link('api')}">API documentation</a>
    </div>
    <h1>
      Overview
    </h1>
    
    <xsl:for-each select="package">
      <a name="{@name}"/>
      <br clear="all"/>
      <h2>
        <a href="{xp:link(concat('api/package?', @name))}">
          <xsl:value-of select="@name"/>
        </a>
      </h2>
      <div class="apidoc">
        <xsl:value-of select="func:first-sentence(comment)" disable-output-escaping="yes"/>
      </div>
      <fieldset class="summary">
        <h3>Packages in <xsl:value-of select="@name"/>:</h3>
        <ul>
          <xsl:for-each select="package">
            <li>
              <a href="{xp:link(concat('api/package?', @name))}">
                <xsl:value-of select="@name"/>
              </a>
              <xsl:if test="string(comment) != ''">
                <em> - <xsl:value-of select="func:first-sentence(comment)" disable-output-escaping="yes"/></em>
              </xsl:if>
            </li>
          </xsl:for-each>
        </ul>
      </fieldset>
    </xsl:for-each>
  </xsl:template>
</xsl:stylesheet>
