<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
 !
 ! $Id: static.xsl 7662 2006-08-13 11:47:23Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:include href="../master.xsl"/>
  <xsl:template match="/">
    <html>
      <body>
        Please login:<br/>
        
        <form action="{func:link('login')}" method="POST">
          <input type="hidden" name="__handler" value="{/formresult/handlers/handler[@name= 'loginhandler']/@id}"/>
          Username: <input type="text" name="username" size="20" value="{/formresult/formvalues/param[@name= 'username']}"/><br/>
          Password: <input type="password" name="password" size="20" value="{/formresult/formvalues/param[@name= 'password']}"/><br/>
          <input type="submit" name="submit" value="Authenticate"/>
        </form>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
