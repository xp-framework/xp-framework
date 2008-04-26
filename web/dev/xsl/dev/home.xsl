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
      <td id="content" style="background: white url(image/apple.jpg) no-repeat bottom left">
        <h1>Developer Zone</h1>
        <p>
        </p>

        <!-- Featured items -->
        <table width="100%" class="columned"><tr>
          <td width="50%" valign="top">
            <h2>Features</h2>
            <ul>
              <li><a href="#">Enterprise Application Server Connectivity</a></li>
              <li><a href="#">O/R-mapping API</a></li>
              <li><a href="#">Collections framework</a></li>
              <li><a href="#">XML/XSL scriptlets</a></li>
              <li><a href="#">Unittests</a></li>
              <li><a href="#">Web services: Client/Server</a></li>
            </ul>
          </td>
          <td width="25%" valign="top">
            <h2>RFCs</h2>
          </td>
          <td width="25%" valign="top">
            <h2>Download</h2>
            <ul>
              <li><a href="#">Latest</a></li>
              <li><a href="#">SVN head</a></li>
            </ul>
          </td>
        </tr></table>
        <br clear="all"/>
      </td>
    </tr></table>
  </xsl:template>

  <xsl:template name="html-title">
    XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
