<?php

class FCM_Fulfillment_Block_GoogleAnalytics_Ga extends Mage_GoogleAnalytics_Block_Ga
{
    /**
     * Render regular page tracking javascript code
     * The custom "page name" may be set from layout or somewhere else. It must start from slash.
     *
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApiBasicConfiguration.html#_gat.GA_Tracker_._trackPageview
     * @link http://code.google.com/apis/analytics/docs/gaJS/gaJSApi_gaq.html
     * @param string $accountId
     * @return string
     */
    protected function _getPageTrackingCode($accountId)
    {
        $pageName   = trim($this->getPageName());
        $optPageURL = '';
        if ($pageName && preg_match('/^\/.*/i', $pageName)) {
            $optPageURL = ", '{$this->jsQuoteEscape($pageName)}'";
        }
        return "
var pluginUrl =
'//www.google-analytics.com/plugins/ga/inpage_linkid.js';
_gaq.push(['_require', 'inpage_linkid', pluginUrl]);
_gaq.push(['_setAccount', '{$this->jsQuoteEscape($accountId)}']);
_gaq.push(['_setDomainName', 'americanswan.com']);
_gaq.push(['_setAllowLinker', true]);
_gaq.push(['_trackPageview'{$optPageURL}]);
";
    }
}
