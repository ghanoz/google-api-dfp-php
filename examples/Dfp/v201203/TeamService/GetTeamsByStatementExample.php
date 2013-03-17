<?php
/**
 * This example gets all teams that you belong to. The statement
 * retrieves up to the maximum page size limit of 500. To create teams, run
 * CreateTeamssExample.php.
 *
 * Tags: TeamService.getTeamsByStatement
 * Tags: UserService.getCurrentUser
 *
 * PHP version 5
 *
 * Copyright 2012, Google Inc. All Rights Reserved.
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
 * @subpackage v201203
 * @category   WebServices
 * @copyright  2012, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Paul Rashidi
 */
error_reporting(E_STRICT | E_ALL);

// You can set the include path to src directory or reference
// DfpUser.php directly via require_once.
// $path = '/path/to/dfp_api_php_lib/src';
$path = dirname(__FILE__) . '/../../../../src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Google/Api/Ads/Dfp/Lib/DfpUser.php';
require_once 'Google/Api/Ads/Common/Util/MapUtils.php';

try {
  // Get DfpUser from credentials in "../auth.ini"
  // relative to the DfpUser.php file's directory.
  $user = new DfpUser();

  // Log SOAP XML request and response.
  $user->LogDefaults();

  // Get the TeamService.
  $teamService = $user->GetService('TeamService', 'v201203');

  // Get the UserService.
  $userService = $user->GetService('UserService', 'v201203');

  // Get the current user's teams.
  $teamIds = $userService->getCurrentUser()->teamIds;

  // Create a statement to only select teams that you belong to sorted
  // by name.
  $filterStatementText = sprintf('WHERE id IN (%s) ORDER BY name LIMIT 500',
      implode(',', $teamIds));
  $filterStatement =
      new Statement($filterStatementText, $vars);

  // Get teams by statement.
  $page = $teamService->getTeamsByStatement($filterStatement);

  // Display results.
  if (isset($page->results)) {
    $i = $page->startIndex;
    foreach ($page->results as $team) {
      print $i . ') Team with ID "' . $team->id
          . '", and name "' . $team->name
          . "\" was found.\n";
       $i++;
    }
  }

  print 'Number of results found: ' . $page->totalResultSetSize . "\n";
} catch (Exception $e) {
  print $e->getMessage() . "\n";
}

