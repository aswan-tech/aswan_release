<?php
require 'globals.php';
require 'oauth_helper.php';

// Fill in the next 3 variables.
$guid='AYXIKZTVOM3PD7JGPD6HSJY5OM';
$access_token='A=1ratqwP7gA3phgmDt6ZgAGeJjIiAVGtfTRmpNamiO9RsJ5FDvO.bcoR6vY5LXthIzTEQOihWgVgTSs8l5jb5E3UukhWuE1GSDANBkm7G0J3ZWmGto3ABBxIknicoM1lYdHsqE0gzYYkZeNRNpo5hYy9T9s.7U35WnkxMpcJJBuYANRmbzoM0g3D5VdMWaJs9DCOSkzZe7.LwH5Kc455PwZIzjiLEN0C2_y0aY7q9TL_kkXlw31cCKOq1Hd0oq4y68STrKMPQyH.TFiDQ77aykJbTy.Did3A_CsVVn7N927YhKMcLnYIHqCEVkaA5SvwsOn70kUiticgsXYHom1AudrBPlIcGlEvG0mHI1KcPzdPvMDGvs1M6_sVaGybh82H9gFDKUUjLCUaHhotO0AHcOlZOxT8087Oa0RkgAx_BxR3LkI6ipdPRbdL_Yv1hBh6tKaOaPrjPOTksqnHUxAiLg3B4Z5NCvj_Fbxg4rm1bSkUGD_08OLKeAZ6FZWul01_MgUVkTrDAwZqFjo0WnETuNNcFFPvv66LcI0ryL.IQvwGGSVxvCrXFwopHa8lF0yhKVpZLCXmljpjnPUPL1e7JwmxSFgxDCZ19maBRm.1QfuYO7BKA3bblH0kgpm58hB1saBabx5cGc2q_ZTSXYE3BuIUJctnPXd1xbh8w0GHlKgpLgs_xUQx_VcFcJP66Lv6lhFW6x3Rk0iSyIGlQSGVtTKqzs2bjkZzgJxtTAtswDrt7FwIzomdCk94SqZNmcbrpmG.rJhJyK4rv_LKonbLWISmIOy9gBoltWiLWvnnI';
$access_token_secret='917fffa58ccc040d73732b4bb6a0eb21f63bac18';

// Call Contact API
$retarr = callcontact(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET,
                      $guid, $access_token, $access_token_secret,
                      false, true);

exit(0);

/**
 * Call the Yahoo Contact API
 * @param string $consumer_key obtained when you registered your app
 * @param string $consumer_secret obtained when you registered your app
 * @param string $guid obtained from getacctok
 * @param string $access_token obtained from getacctok
 * @param string $access_token_secret obtained from getacctok
 * @param bool $usePost use HTTP POST instead of GET
 * @param bool $passOAuthInHeader pass the OAuth credentials in HTTP header
 * @return response string with token or empty array on error
 */
function callcontact($consumer_key, $consumer_secret, $guid, $access_token, $access_token_secret, $usePost=false, $passOAuthInHeader=true)
{
  $retarr = array();  // return value
  $response = array();

  $url = 'http://social.yahooapis.com/v1/user/' . $guid . '/contacts;count=5';
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
      oauth_compute_hmac_sig($usePost? 'POST' : 'GET', $url, $params,
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
    logit("callcontact:INFO:request_url:$request_url");
    $response = do_get($request_url, 80, $headers);
  }

  // extract successful response
  if (! empty($response)) {
    list($info, $header, $body) = $response;
    if ($body) {
      logit("callcontact:INFO:response:");
      print(json_pretty_print($body));
    }
    $retarr = $response;
  }

  return $retarr;
}
?>
