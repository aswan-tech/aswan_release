<?php

/**
 * Invitation frontend controller
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class FCM_Socialapi_IndexController extends Mage_Core_Controller_Front_Action {

    public function thankyouAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function facebookAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendFacebookAction() {
        $message = $this->getRequest()->getPost('facebook_message');
        $appid = Mage::getStoreConfig('socialapi/facebook/appid');
        $redirect_uri = Mage::getStoreConfig('socialapi/facebook/redirecturi');

        echo "<script type='text/javascript'>
                window.location = 'https://www.facebook.com/dialog/apprequests?app_id=" . $appid . "&message=" . $message . "&redirect_uri=" . $redirect_uri . "'
            </script>";
    }

    public function fbresponseAction() {

        if ($_GET['error_code']) {
            Mage::getSingleton('core/session')->addError("Error was encountered while sending facebook invite");
            Mage::log("Error was encountered while sending gmail invite ::  " . $_GET['error_code'] . "\n" . $_GET['error_msg'], 0, "Social.log", true);
            $this->_redirect('socialapi/index/thankyou');
        } else {
            $string = "Invitation sent to ";
            $names = array();
            foreach ($_GET['to'] as $frnd_id) {
                $names[] = $this->getName($frnd_id);
            }
            $string .= implode(", ", $names);
            $string .= " successfully";

            Mage::getSingleton('core/session')->addSuccess($string);
            $this->_redirect('socialapi/index/thankyou');
        }
    }

    function getName($id) {
        $facebookUrl = "https://graph.facebook.com/" . $id;
        $str = file_get_contents($facebookUrl);
        $result = json_decode($str);
        return $result->name;
    }

    public function gmailAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendGmailAction() {

        $store = Mage::app()->getStore();
        $contacts = $this->getRequest()->getPost('gmail_contact');
        if (empty($contacts)) {
            Mage::getSingleton('core/session')->addError("Please select contacts to send invite to, or close the dialog");
            $this->_redirect('social/index/gmail');
        }

        $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email'); // sender email address
        $fromName = Mage::getStoreConfig('trans_email/ident_general/name'); // sender name

        $body = $this->getRequest()->getPost('gmail_message');
        $body = nl2br($body);
        $body = str_replace('{{var store.getFrontendName()}}', $store->getName(), $body);
        $body = str_replace('{{store url=""}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), $body);
        $body = str_replace('{{skin url="images/logo_email.gif"}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/enterprise/lecom/images/logo_email.gif', $body);


        $subject = Mage::getStoreConfig('socialapi/gmail/invitesubject');

        $contacts = array_flip($contacts);

        $toEmailArray = array();
        $toNameArray = array();

        foreach ($contacts as $toEmail => $toName) {
            $toEmailArray[] = $toEmail;
            $toNameArray[] = $toName;
        }

        $emailTemplate = Mage::getModel('core/email_template')->loadByCode('social_invite');
        $emailTemplateVariables = array();
        $emailTemplateVariables['message'] = $body;
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $emailTemplate->setTemplateSubject($subject);
        $emailTemplate->setSenderName($fromName);
        $emailTemplate->setSenderEmail($fromEmail);

        try {
            $emailTemplate->send($toEmailArray, $toNameArray, $processedTemplate);
            $string = "Invitation sent to ";
            $string .= implode(", ", $toNameArray);
            $string .= " successfully";
            Mage::getSingleton('core/session')->addSuccess($string);
            $this->_redirect('socialapi/index/thankyou');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError("Some Error was encountered while sending the invite");
            Mage::log("Error was encountered while sending gmail invite ::  " . $ex->getMessage() . "\n" . $ex->getTraceAsString(), 0, "Social.log", true);
            $this->_redirect('socialapi/index/thankyou');
            return false;
        }
    }

    public function yahooAction() {

        require_once("yahooapisrc/globals.php");
        require_once("yahooapisrc/oauth_helper.php");

        $callback = Mage::getStoreConfig('socialapi/yahoo/callbackurl');

        $retarr = $this->get_request_token(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                        $callback, true, true, true);

        if (!empty($retarr)) {
            list($info, $headers, $body, $body_parsed) = $retarr;

            $_SESSION['oauth_token_secret'] = $body_parsed['oauth_token_secret'];
            if ($info['http_code'] == 200 && !empty($body)) {
                //print "Have the user go to xoauth_request_auth_url to authorize your app\n" . rfc3986_decode($body_parsed['xoauth_request_auth_url']) . "\n";
                header('Location: ' . rfc3986_decode($body_parsed['xoauth_request_auth_url']));
            }
        }

        exit(0);
    }

    public function yahoo2Action() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendYahooAction() {

        $store = Mage::app()->getStore();
        $fromEmail = Mage::getStoreConfig('trans_email/ident_general/email'); // sender email address
        $fromName = Mage::getStoreConfig('trans_email/ident_general/name'); // sender name

        $body = $this->getRequest()->getPost('yahoo_message');
        $body = nl2br($body);
        $body = str_replace('{{var store.getFrontendName()}}', $store->getName(), $body);
        $body = str_replace('{{store url=""}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), $body);
        $body = str_replace('{{skin url="images/logo_email.gif"}}', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/enterprise/lecom/images/logo_email.gif', $body);


        $subject = Mage::getStoreConfig('socialapi/yahoo/invitesubject');
        $contacts = $this->getRequest()->getPost('yahoo_contact');
        $contacts = array_flip($contacts);

        $toEmailArray = array();
        $toNameArray = array();

        foreach ($contacts as $toEmail => $toName) {
            $toEmailArray[] = $toEmail;
            $toNameArray[] = $toName;
        }

        $emailTemplate = Mage::getModel('core/email_template')->loadByCode('social_invite');
        $emailTemplateVariables = array();
        $emailTemplateVariables['message'] = $body;
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
        $emailTemplate->setTemplateSubject($subject);
        $emailTemplate->setSenderName($fromName);
        $emailTemplate->setSenderEmail($fromEmail);

        try {
            $emailTemplate->send($toEmailArray, $toNameArray, $processedTemplate);
            $string = "Invitation sent to ";
            $string .= implode(", ", $toNameArray);
            $string .= " successfully";
            Mage::getSingleton('core/session')->addSuccess($string);
            $this->_redirect('socialapi/index/thankyou');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError("Some Error was encountered while sending the invite");
            Mage::log("Error was encountered while sending yahoo invite ::  " . $ex->getMessage() . "\n" . $ex->getTraceAsString(), 0, "Social.log", true);
            $this->_redirect('socialapi/index/thankyou');
            return false;
        }
    }

    public function twitterAction() {

        require_once("twitterapisrc/twitteroauth/twitteroauth.php");
        require_once("twitterapisrc/config.php");

        /* If access tokens are not available redirect to connect page. */
        if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
            header('Location: ./clearSessions');
        }

        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

        /* Save temporary credentials to session. */
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        echo $connection->http_code;
        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                echo $url = $connection->getAuthorizeURL($token);
                header('Location: ' . $url);
                exit;
                break;
            default:
                /* Show notification if something went wrong. */
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    }

    public function callbackAction() {

        if (isset($_GET["denied"])) {
            echo "<script>window.close();</script>";
            return;
        }

        require_once("twitterapisrc/twitteroauth/twitteroauth.php");
        require_once("twitterapisrc/config.php");

        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
            $_SESSION['oauth_status'] = 'oldtoken';
            header('Location: ./clearSessions');
            exit;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function sendTwitterAction() {

        require_once("twitterapisrc/twitteroauth/twitteroauth.php");
        require_once("twitterapisrc/config.php");

        /* Get user access tokens out of the session. */
        $access_token = $_SESSION['access_token'];

        /* Create a TwitterOauth object with consumer/user tokens. */
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

        $user = $connection->get('account/verify_credentials');
        $contacts = $this->getRequest()->getPost('twitter_contact');
        $names = array();
        $errors = array();

        foreach ($contacts as $contact_id) {
            $parameters = array('user_id' => $contact_id, 'text' => $this->getRequest()->getPost('twitter_message'));
            $method = 'direct_messages/new';
            $response = $connection->post($method, $parameters);

            $names[] = $response->recipient_screen_name;

            $json = json_encode($response);
            $array_response = json_decode($json, TRUE);
            $errors[] = $array_response['errors']['0']['message'];

        }

        $names = array_filter($names);
        $errors = array_filter($errors);

        if (count($names) > 0) {
            $string = "Invitation sent to ";
            $string .= implode(", ", $names);
            $string .= " successfully";
            Mage::getSingleton('core/session')->addSuccess($string);
            $this->_redirect('socialapi/index/thankyou');
        } else {
            $string .= implode(", ", $errors);
            Mage::getSingleton('core/session')->addError($string);
            $this->_redirect('socialapi/index/thankyou');
        }
    }

    public function clearSessionsAction() {

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);
        unset($_SESSION['access_token']);
        /* Save HTTP status for error dialog on connnect page. */
        header('Location: /invitation/index/send/');
        exit;
    }

    /**
     * Get a request token.
     * @param string $consumer_key obtained when you registered your app
     * @param string $consumer_secret obtained when you registered your app
     * @param string $callback callback url can be the string 'oob'
     * @param bool $usePost use HTTP POST instead of GET
     * @param bool $useHmacSha1Sig use HMAC-SHA1 signature
     * @param bool $passOAuthInHeader pass OAuth credentials in HTTP header
     * @return array of response parameters or empty array on error
     */
    function get_request_token($consumer_key, $consumer_secret, $callback, $usePost=false, $useHmacSha1Sig=true, $passOAuthInHeader=false) {
        $retarr = array();  // return value
        $response = array();

        $url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_timestamp'] = time();
        $params['oauth_consumer_key'] = $consumer_key;
        $params['oauth_callback'] = $callback;

        // compute signature and add it to the params list
        if ($useHmacSha1Sig) {
            $params['oauth_signature_method'] = 'HMAC-SHA1';
            $params['oauth_signature'] =
                    oauth_compute_hmac_sig($usePost ? 'POST' : 'GET', $url, $params,
                            $consumer_secret, null);
        } else {
            $params['oauth_signature_method'] = 'PLAINTEXT';
            $params['oauth_signature'] =
                    oauth_compute_plaintext_sig($consumer_secret, null);
        }

        // Pass OAuth credentials in a separate header or in the query string
        if ($passOAuthInHeader) {

            $query_parameter_string = oauth_http_build_query($params, FALSE);

            $header = build_oauth_header($params, "yahooapis.com");
            $headers[] = $header;
        } else {
            $query_parameter_string = oauth_http_build_query($params);
        }

        // POST or GET the request
        if ($usePost) {
            $request_url = $url;
            logit("getreqtok:INFO:request_url:$request_url");
            logit("getreqtok:INFO:post_body:$query_parameter_string");
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $response = do_post($request_url, $query_parameter_string, 443, $headers);
        } else {
            $request_url = $url . ($query_parameter_string ?
                            ('?' . $query_parameter_string) : '' );

            //logit("getreqtok:INFO:request_url:$request_url");

            $response = do_get($request_url, 443, $headers);
        }

        // extract successful response
        if (!empty($response)) {
            list($info, $header, $body) = $response;
            $body_parsed = oauth_parse_str($body);
            if (!empty($body_parsed)) {
                //logit("getreqtok:INFO:response_body_parsed:");
                //print_r($body_parsed);
            }
            $retarr = $response;
            $retarr[] = $body_parsed;
        }

        return $retarr;
    }

}
