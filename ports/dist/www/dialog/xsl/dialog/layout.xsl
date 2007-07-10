<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Layout stylesheet
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
  <xsl:include href="../master.xsl"/>
  <xsl:include href="links.inc.xsl"/>
  
  <!--
   ! Template that matches on the root node
   !
   ! @purpose  Define the site layout
   !-->
  <xsl:template match="/">
    <html>
      <head>
        <title>
          <xsl:call-template name="page-title"/>
        </title>
        <link rel="stylesheet" href="/{/formresult/config/style}.css"/>
        <link rel="alternate" type="application/rss+xml" title="RSS - {/formresult/config/title}" href="/rss/"/>
        <script language="JavaScript"><![CDATA[
          function handleKey(event) {
            if (event.ctrlKey || event.altKey || event.shiftKey) return false;

            switch (event.keyCode) {
              case 37:  // Left arrow
                if (
                  (element= document.getElementById('previous')) &&
                  (element.href)
                ) {
                  document.location.href= element.href;
                  return false;
                }
                break;
              
              case 39:  // Right arrow
                if (
                  (element= document.getElementById('next')) &&
                  (element.href)
                ) {
                  document.location.href= element.href;
                  return false;
                }
                break;
              
            }
            
            return true;
          }
        ]]></script>
      </head>
      <body onKeyUp="handleKey(event)">
        <center>
          <!-- main content -->
          <table border="0" cellspacing="0" class="main">
            <tr id="header">
              <td width="60" class="gutter" id="gutter1">&#160;</td>
              <td width="60" class="gutter" id="gutter2">&#160;</td>
              <td width="60" class="gutter" id="gutter3">&#160;</td>
              <td width="690" class="gutter" valign="bottom" align="right">
                <a class="nav" id="active" href="{func:linkPage(0)}">
                  <xsl:value-of select="/formresult/config/title"/>: Home
                </a>
              </td>
            </tr>
            <tr>
              <td colspan="4">
                <div class="content">
                  <xsl:call-template name="content"/>
                </div>
              </td>
            </tr>
          </table>

          <!-- footer -->
          <table border="0" cellspacing="0" cellpadding="2" class="footer">
            <tr>
              <td><small>&#169; <xsl:value-of select="/formresult/config/copyright"/></small></td>
              <td align="right">
                <a href="http://xp-framework.net/">
                  <img border="0" src="/image/powered_by_xp.png" width="80" height="15" alt="XP powered"/>
                </a>
              </td>
            </tr>
          </table>
        </center>
      </body>
    </html>
  </xsl:template>

  <xsl:template name="page-title">
    <xsl:value-of select="$__state"/> - 
    <xsl:value-of select="/formresult/config/title"/>
  </xsl:template>
</xsl:stylesheet>
