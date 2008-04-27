<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC criteria include
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

  <xsl:variable name="criteria">
    <criteria id="status">
      <filter id="draft">Draft</filter>
      <filter id="discussion">Discussion</filter>
      <filter id="implemented">Implemented</filter>
      <filter id="obsoleted">Obsoleted</filter>
      <filter id="rejected">Rejected</filter>
    </criteria>
  </xsl:variable>

</xsl:stylesheet>
