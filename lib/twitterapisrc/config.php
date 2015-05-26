<?php

/**
 * @file
 * A single location to store configuration.
 */
define('CONSUMER_KEY', Mage::getStoreConfig('socialapi/twitter/consumerkey'));
define('CONSUMER_SECRET', Mage::getStoreConfig('socialapi/twitter/consumersecret'));
define('OAUTH_CALLBACK', Mage::getStoreConfig('socialapi/twitter/oauthcallback'));
