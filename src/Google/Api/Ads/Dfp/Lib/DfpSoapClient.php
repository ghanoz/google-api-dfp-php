<?php
/**
 * Copyright 2011, Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package    GoogleApiAdsDfp
 * @subpackage Lib
 * @category   WebServices
 * @copyright  2011, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author     Adam Rogal
 * @author     Eric Koleda
 * @author     Paul Rashidi
 * @author     Vincent Tsao
 * @see        AdsSoapClient
 */
require_once 'Google/Api/Ads/Common/Lib/AdsSoapClient.php';

/**
 * An extension of the {@link AdsSoapClient} for Google's DoubleClick for
 * Publishers API.
 */
class DfpSoapClient extends AdsSoapClient {

  /**
   * Constructor for Google's DoubleClick for Publishers API SOAP client.
   * @param string $wsdl URI of the WSDL file or <var>NULL</var> if working in
   *     non-WSDL mode
   * @param array $options the SOAP client options
   * @param AdsUser $user the user which is responsible for this client
   * @param string $serviceName the name of the service which is making this
   *     call
   * @param string $serviceNamespace the namespace of the service
   */
  public function __construct($wsdl, array $options, AdsUser $user,
      $serviceName, $serviceNamespace) {
    parent::__construct($wsdl, $options, $user, $serviceName,
        $serviceNamespace);
  }

  /**
   * Generates the SOAP header for the client.
   * @return SoapHeader the instantiated SoapHeader ready to set
   * @access protected
   */
  protected function GenerateSoapHeader() {
    $headerObject = $this->Create('SoapRequestHeader');
    foreach (get_object_vars($headerObject) as $var => $value) {
      switch ($var) {
        case 'authentication':
          if ($this->user->GetOAuthInfo() != NULL) {
            $oauthParameters =
                $this->user->GetOAuthHandler()->GetSignedRequestParameters(
                    $this->user->GetOAuthInfo(), $this->location);
            $authentication = $this->Create('OAuth');
            $authentication->parameters = 'OAuth '
                . $this->user->GetOAuthHandler()->FormatParametersForHeader(
                    $oauthParameters);
          } else if ($this->user->GetOAuth2Info() != NULL) {
            if ($this->user->IsOAuth2AccessTokenValid() &&
                $this->user->CanRefreshOAuth2AccessToken()) {
              $oAuth2Info = $this->user->RefreshOAuth2AccessToken();
            }
            if ($this->user->IsOAuth2AccessTokenValid()) {
              $oauth2Parameters = $this->user->GetOAuth2Info();
              $authentication = $this->Create('OAuth');
              $authentication->parameters = $this->user->GetOAuth2Handler()->
                  FormatCredentialsForHeader($oauth2Parameters);
            }
          } else {
            $authentication = $this->Create('ClientLogin');
            $authentication->token = $this->GetHeaderValue('authToken');
          }
          $headerObject->$var = $authentication;
          break;
        default:
          $headerObject->$var = $this->GetHeaderValue($var);
      }
    }
    return new SoapHeader($this->serviceNamespace, 'RequestHeader',
        $headerObject, FALSE);
  }

  /**
   * Removes the authentication information from the request before being
   * logged.
   * @param string $request the request with sensitive data to remove
   * @return string the request with the authentication token removed
   * @access protected
   */
  protected function RemoveSensitiveInfo($request) {
    $tags = array('authToken', 'authentication');
    $regexFormat = '/(<(?:[^:]+:)?%s(?:\s[^>]*)?>).*(<\/(?:[^:]+:)?%s\s*>)/sU';
    $result = $request;
    foreach ($tags as $tag) {
      $regex = sprintf($regexFormat, $tag, $tag);
      $result = preg_replace($regex, '\1*****\2', $result);
    }
    return isset($result) ? $result : $request;
  }

  /**
   * Generates the request info message containing:
   * <ul>
   * <li>email</li>
   * <li>service</li>
   * <li>method</li>
   * <li>responseTime</li>
   * <li>requestId</li>
   * <li>server</li>
   * <li>isFault</li>
   * <li>faultMessage</li>
   * </ul>
   * @return string the request info message to log
   * @access protected
   */
  protected function GenerateRequestInfoMessage() {
    return 'email=' . $this->GetEmail() . ' service=' . $this->GetServiceName()
        . ' method=' . $this->GetLastMethodName() . ' responseTime='
        . $this->GetLastResponseTime() . ' requestId='
        . $this->GetLastRequestId() . ' server=' . $this->GetServer()
        . ' isFault=' . $this->IsFault() . ' faultMessage='
        . $this->GetLastFaultMessage();
  }
}

