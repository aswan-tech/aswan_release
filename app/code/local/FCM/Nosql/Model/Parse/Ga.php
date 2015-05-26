<?php 
class FCM_Nosql_Model_Parse_Ga extends Mage_Core_Model_Abstract
{
    public function getCookies() {
        $cookie = Mage::getModel('core/cookie');
        $utmz = $cookie->get('__utmz');
        $utma = $cookie->get('__utma');
        return $this->parseCookies($utmz, $utma);
    }
    
    public function parseCookies($utmz, $utma) {
        $cookies = array();
        if ($utmz) {
            $cookies['campaign'] = $this->parseCampaign($utmz);
        }
        if ($utma) {
            $cookies['visit'] = $this->parseVisit($utma);
        }
        return $cookies;
    }

    public function parseCampaign($utmz) {
        $campaign = array();
        // Parse __utmz cookie
        list($domainHash, $timestamp, $sessionNumber, $campaignNumber, $campaignData) = explode('.', $utmz);

        // Parse the campaign data
        parse_str(strtr($campaignData, "|", "&"), $campaignArray);

        if (!isset($campaignArray['utmgclid'])) {
            $campaign['source']  = $this->getArrayValue('utmcsr', $campaignArray);
            $campaign['name']    = $this->getArrayValue('utmccn', $campaignArray);
            $campaign['medium']  = $this->getArrayValue('utmcmd', $campaignArray);
            $campaign['term']    = $this->getArrayValue('utmctr', $campaignArray);
            $campaign['content'] = $this->getArrayValue('utmcct', $campaignArray);
        } else {
            // The gclid is ONLY present when auto tagging has been enabled.
            // All other variables, except the term variable, will be '(not set)'.
            // Because the gclid is only present for Google AdWords we can
            // populate some other variables that would normally
            // be left blank.
            $campaign['source']  = "google";
            $campaign['name']    = "";
            $campaign['medium']  = "cpc";
            $campaign['term']    = $campaignArray['utmctr'];
            $campaign['content'] = "";
        }

        return $campaign;
    }

    public function parseVisit($utma) {
        $visit = array();
        // Parse the __utma cookie
        list($domainHash, $randomId, $initialVisit, $previousVisit,
            $currentVisit, $sessionCounter) = explode('.', $utma);

        $visit['first']       = date("Y-m-d H:i:s", $initialVisit);
        $visit['previous']    = date("Y-m-d H:i:s", $previousVisit);
        $visit['current']     = date("Y-m-d H:i:s", $currentVisit);
        $visit['count']       = $sessionCounter;

        return $visit;
    }

    /**
     * Checks if the array key isset before trying to get value.
     * Prevents logging of the error: "Notice: Undefined index: ..."
     * @param  $key
     * @return void
     */
    public function getArrayValue($key, $array) {
        return isset($array[$key]) ? $array[$key] : null;
    }
}