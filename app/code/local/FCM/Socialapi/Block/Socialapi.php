<?php

class FCM_Socialapi_Block_Socialapi extends Mage_Core_Block_Template {

    protected function _construct() {
        $this->addData(array(
            'cache_lifetime' => 1,
            'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG),
            'cache_key' => 'socialapi_' . time()
        ));
    }

    function curl_file_get_contents($url) {
        $curl = curl_init();
        $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

        curl_setopt($curl, CURLOPT_URL, $url); //The URL to fetch. This can also be set when initializing a session with curl_init().
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5); //The number of seconds to wait while trying to connect.

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
        curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //To stop cURL from verifying the peer's certificate.
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $contents = curl_exec($curl);
        curl_close($curl);
        return $contents;
    }

    public function gmail() {

        if(isset($_GET["error"]) && $_GET["error"] == 'access_denied'){
            echo "<script>window.close();</script>";
            return;
        }

        $client_id = Mage::getStoreConfig('socialapi/gmail/clientid');
        $client_secret = Mage::getStoreConfig('socialapi/gmail/clientsecret');
        $redirect_uri = Mage::getStoreConfig('socialapi/gmail/redirecturi');
        $max_results = 5000;

        $auth_code = $_GET["code"];

        $fields = array(
            'code' => urlencode($auth_code),
            'client_id' => urlencode($client_id),
            'client_secret' => urlencode($client_secret),
            'redirect_uri' => urlencode($redirect_uri),
            'grant_type' => urlencode('authorization_code')
        );
        $post = '';
        foreach ($fields as $key => $value) {
            $post .= $key . '=' . $value . '&';
        }
        $post = rtrim($post, '&');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($curl, CURLOPT_POST, 5);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($result);
        $accesstoken = $response->access_token;

        $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max_results . '&oauth_token=' . $accesstoken;
        $xmlresponse = $this->curl_file_get_contents($url);
        if ((strlen(stristr($xmlresponse, 'Authorization required')) > 0) && (strlen(stristr($xmlresponse, 'Error ')) > 0)) {
            echo "<h2>OOPS !! Something went wrong. Please try reloading the page.</h2>";
            exit();
        }

        $xml = new SimpleXMLElement($xmlresponse);

        $xml->registerXPathNamespace('c', 'http://www.w3.org/2005/Atom');

        $result = $xml->xpath('//c:entry');

        $gmail_contacts = array();

        foreach ($result as $value) {
            $email = $value->children("http://schemas.google.com/g/2005")->attributes()->address;
            $title = $value->title;

            $json = json_encode($email);
            $array_email = json_decode($json, TRUE);

            $json = json_encode($title);
            $array_title = json_decode($json, TRUE);

            $gmail_contacts[$array_email['0']] = empty($array_title['0']) ? $array_email['0'] : $array_title['0'];
        }

        return $gmail_contacts;
    }

    public function twitterCallback() {

        require_once("twitterapisrc/twitteroauth/twitteroauth.php");
        require_once("twitterapisrc/config.php");

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            $_SESSION['status'] = 'verified';


            /* Get user access tokens out of the session. */
            $access_token = $_SESSION['access_token'];

            /* Create a TwitterOauth object with consumer/user tokens. */
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

            /* If method is set change API call made. Test is called by default. */

            /* Get logged in user to help with tests. */
            $user = $connection->get('account/verify_credentials');

            /* https://api.twitter.com/1.1/friends/ids.json?cursor=-1&screen_name=twitterapi&count=5000 */

            $parameters = array('user_id' => $user->id, 'count' => 5000);
            $method = 'friends/ids';
            $dm = $connection->get($method, $parameters);

            $ids = implode(",", $dm->ids);

            $parameters = array('user_id' => $ids, 'include_entities' => false);
            $method = 'users/lookup';
            $twitter_contacts = $connection->get($method, $parameters);
            $return_array = array();

            foreach ($twitter_contacts as $twitter_contact) {
                $return_array[$twitter_contact->id] = array(
                    'screenname' => $twitter_contact->screen_name,
                    'name' => $twitter_contact->name
                );
            }

            return $return_array;
        } else {
            /* Save HTTP status for error dialog on connnect page. */
            header('Location: ./clearSessions');
            exit;
        }
    }

    public function yahoo() {

        require_once("yahooapisrc/globals.php");
        require_once("yahooapisrc/oauth_helper.php");

        $request_token = $_GET['oauth_token'];
        $request_token_secret = $_SESSION['oauth_token_secret'];
        $oauth_verifier = $_GET['oauth_verifier'];

// Get the access token using HTTP GET and HMAC-SHA1 signature
        $retarr = $this->get_access_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                        $request_token, $request_token_secret,
                        $oauth_verifier, false, true, true);

        if (!empty($retarr)) {
            list($info, $headers, $body, $body_parsed) = $retarr;
            if ($info['http_code'] == 200 && !empty($body)) {
                //print "Use oauth_token as the token for all of your API calls:\n" .
                rfc3986_decode($body_parsed['oauth_token']) . "\n";
            }
        }

        // Fill in the next 3 variables.
        $guid = $body_parsed['xoauth_yahoo_guid'];
        $access_token = rfc3986_decode($body_parsed['oauth_token']);
        $access_token_secret = $body_parsed['oauth_token_secret'];

// Call Contact API
        $retarr2 = $this->callcontact(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                        $guid, $access_token, $access_token_secret,
                        false, true);


        // extract successful response
        if (!empty($retarr2)) {
            list($info, $header, $body) = $retarr2;
            if ($body) {
                $json = json_pretty_print($body);
                $array_contacts = json_decode($json, TRUE);
            }
        }
        $return_contacts = array();

        foreach ($array_contacts['contacts']['contact'] as $yahoo_contact) {

            foreach ($yahoo_contact['fields'] as $field) {
                if ($field['type'] == 'name') {
                    $return_contacts[$yahoo_contact['id']]['name'] = $field['value']['givenName'] . " " . $field['value']['familyName'];
                }

                if ($field['type'] == 'yahooid') {
                    $return_contacts[$yahoo_contact['id']]['email'] = $field['value'] . "@yahoo.com";
                } elseif ($field['type'] == 'otherid') {
                    $return_contacts[$yahoo_contact['id']]['email'] = $field['value'];
                }
            }
        }

        return $return_contacts;
    }

    function get_access_token($consumer_key, $consumer_secret, $request_token, $request_token_secret, $oauth_verifier, $usePost=false, $useHmacSha1Sig=true, $passOAuthInHeader=true) {
        $retarr = array();  // return value
        $response = array();

        $url = 'https://api.login.yahoo.com/oauth/v2/get_token';
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_timestamp'] = time();
        $params['oauth_consumer_key'] = $consumer_key;
        $params['oauth_token'] = $request_token;
        $params['oauth_verifier'] = $oauth_verifier;

        // compute signature and add it to the params list
        if ($useHmacSha1Sig) {
            $params['oauth_signature_method'] = 'HMAC-SHA1';
            $params['oauth_signature'] =
                    oauth_compute_hmac_sig($usePost ? 'POST' : 'GET', $url, $params,
                            $consumer_secret, $request_token_secret);
        } else {
            $params['oauth_signature_method'] = 'PLAINTEXT';
            $params['oauth_signature'] =
                    oauth_compute_plaintext_sig($consumer_secret, $request_token_secret);
        }

        // Pass OAuth credentials in a separate header or in the query string
        if ($passOAuthInHeader) {
            $query_parameter_string = oauth_http_build_query($params, false);
            $header = build_oauth_header($params, "yahooapis.com");
            $headers[] = $header;
        } else {
            $query_parameter_string = oauth_http_build_query($params);
        }

        // POST or GET the request
        if ($usePost) {
            $request_url = $url;
            logit("getacctok:INFO:request_url:$request_url");
            logit("getacctok:INFO:post_body:$query_parameter_string");
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $response = do_post($request_url, $query_parameter_string, 443, $headers);
        } else {
            $request_url = $url . ($query_parameter_string ?
                            ('?' . $query_parameter_string) : '' );
            //logit("getacctok:INFO:request_url:$request_url");
            $response = do_get($request_url, 443, $headers);
        }

        // extract successful response
        if (!empty($response)) {
            list($info, $header, $body) = $response;
            $body_parsed = oauth_parse_str($body);
            if (!empty($body_parsed)) {
                //logit("getacctok:INFO:response_body_parsed:");
                //print_r($body_parsed);
            }
            $retarr = $response;
            $retarr[] = $body_parsed;
        }

        return $retarr;
    }

    function callcontact($consumer_key, $consumer_secret, $guid, $access_token, $access_token_secret, $usePost=false, $passOAuthInHeader=true) {
        $retarr = array();  // return value
        $response = array();

        $url = 'http://social.yahooapis.com/v1/user/' . $guid . '/contacts;count=5000';
        $params['format'] = 'json';
        $params['view'] = 'compact';
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_timestamp'] = time();
        $params['oauth_consumer_key'] = $consumer_key;
        $params['oauth_token'] = $access_token;

        // compute hmac-sha1 signature and add it to the params list
        $params['oauth_signature_method'] = 'HMAC-SHA1';
        $params['oauth_signature'] =
                oauth_compute_hmac_sig($usePost ? 'POST' : 'GET', $url, $params,
                        $consumer_secret, $access_token_secret);

        // Pass OAuth credentials in a separate header or in the query string
        if ($passOAuthInHeader) {
            $query_parameter_string = oauth_http_build_query($params, true);
            $header = build_oauth_header($params, "yahooapis.com");
            $headers[] = $header;
        } else {
            $query_parameter_string = oauth_http_build_query($params);
        }

        // POST or GET the request
        if ($usePost) {
            $request_url = $url;
            logit("callcontact:INFO:request_url:$request_url");
            logit("callcontact:INFO:post_body:$query_parameter_string");
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $response = do_post($request_url, $query_parameter_string, 80, $headers);
        } else {
            $request_url = $url . ($query_parameter_string ?
                            ('?' . $query_parameter_string) : '' );
            //logit("callcontact:INFO:request_url:$request_url");
            $response = do_get($request_url, 80, $headers);
        }

        // extract successful response
        if (!empty($response)) {
            list($info, $header, $body) = $response;
            if ($body) {
                //logit("callcontact:INFO:response:");
                //print(json_pretty_print($body));
            }
            $retarr = $response;
        }

        return $retarr;
    }

    function limit_words($string, $word_limit) {
        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit));
    }

}