<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* Load required lib files. */
session_start();
require_once('/var/www/Lecom_Magento/twitteroauth-master/twitteroauth/twitteroauth.php');
require_once('/var/www/Lecom_Magento/twitteroauth-master/config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* If method is set change API call made. Test is called by default. */
//$content = $connection->get('account/verify_credentials');

/* Get logged in user to help with tests. */
$user = $connection->get('account/verify_credentials');


function twitteroauth_row($method, $response, $http_code, $parameters = '') {
  echo '<tr>';
  echo "<td><b>{$method}</b></td>";
  switch ($http_code) {
    case '200':
    case '304':
      $color = 'green';
      break;
    case '400':
    case '401':
    case '403':
    case '404':
    case '406':
      $color = 'red';
      break;
    case '500':
    case '502':
    case '503':
      $color = 'orange';
      break;
    default:
      $color = 'grey';
  }
  echo "<td style='background: {$color};'>{$http_code}</td>";
  if (!is_string($response)) {
    $response = print_r($response, TRUE);
  }
  if (!is_string($parameters)) {
    $parameters = print_r($parameters, TRUE);
  }
  echo '<td>', strlen($response), '</td>';
  echo '<td>', $parameters, '</td>';
  echo '</tr><tr>';
  echo '<td colspan="4">', substr($response, 0, 400), '...</td>';
  echo '</tr>';

}

function twitteroauth_header($header) {
  echo '<tr><th colspan="4" style="background: grey;">', $header, '</th></tr>';
}

/* Start table. */
echo '<br><br>';
echo '<table border="1" cellpadding="2" cellspacing="0">';
echo '<tr>';
echo '<th>API Method</th>';
echo '<th>HTTP Code</th>';
echo '<th>Response Length</th>';
echo '<th>Parameters</th>';
echo '</tr><tr>';
echo '<th colspan="4">Response Snippet</th>';
echo '</tr>';


/**
 * Direct Message Methdos.
 */
twitteroauth_header('Direct Message Methods');

/* direct_messages/new */
$parameters = array('user_id' => $user->id, 'text' => 'Testing out @oauthlib code');
$method = 'direct_messages/new';
$dm = $connection->post($method, $parameters);
twitteroauth_row($method, $dm, $connection->http_code, $parameters);

/* direct_messages */
$method = 'direct_messages';
twitteroauth_row($method, $connection->get($method), $connection->http_code);

/* direct_messages/sent */
$method = 'direct_messages/sent';
twitteroauth_row($method, $connection->get($method), $connection->http_code);

/* direct_messages/sent */
$method = "direct_messages/destroy/{$dm->id}";
twitteroauth_row($method, $connection->delete($method), $connection->http_code);



/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/update', array('status' => date(DATE_RFC822)));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

/* Include HTML to display on the page */
include('html.inc');
