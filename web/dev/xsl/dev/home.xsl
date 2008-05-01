<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! Overview page
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

  <xsl:include href="layout.inc.xsl"/>

  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10"><tr>
      <td id="content" style="background: white url(/image/apple.jpg) no-repeat bottom left">
        <h1>Developer Zone</h1>
        <p>
          This is the  <!-- TODO -->
        </p>
        <br clear="all"/>

        <!-- Featured items -->
        <table width="100%" class="columned"><tr>
          <td width="50%" valign="top">
            <h2>Features</h2>
            <p>
              ...
            </p>
            <ul>
              <li><a href="{xp:link('rfc')}">RFC Overview</a></li>
              <li><a href="{xp:link('static?checkout')}">SVN checkout instructions</a></li>
            </ul>
          </td>
          <td width="25%" valign="top">
            <h2>Unittests</h2>
          </td>
          <td width="25%" valign="top">
            <h2>Bugzilla</h2>
          </td>
        </tr></table>
        <br clear="all"/>

        <!-- Move the apple down a bit -->
        <div style="height: 240px">&#160;</div>
      </td>
    </tr></table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
