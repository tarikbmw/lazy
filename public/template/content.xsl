<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:import href="main.xsl"/>
    <xsl:template match="entry" mode="content">
        <li>
            <h2><xsl:value-of select="@title"/></h2>
            <p class="date"><strong>Date </strong><xsl:value-of select="@created"/></p>
            <p><xsl:value-of select="text"/></p>
        </li>
    </xsl:template>
</xsl:stylesheet>
