<?php
/**
 * This example gets all line item creative associations for a given line
 * item ID. The statement retrieves up to the maximum page size limit of 500. To
 * create LICAs, run CreateLicasExample.php.
 *
 * Tags: LineItemCreativeAssociationService.getLineItemCreativeAssociationsByStatement
 *
 * PHP version 5
 *
 * Copyright 2013, Google Inc. All Rights Reserved.
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
 * @subpackage v201302
 * @category   WebServices
 * @copyright  2013, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Adam Rogal
 * @author     Eric Koleda
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

  // Get the LineItemCreativeAssociationService.
  $licaService =
      $user->GetService('LineItemCreativeAssociationService', 'v201302');

  // Set the line item to get LICAs by.
  $lineItemId = 'INSERT_LINE_ITEM_ID_HERE';

  // Create bind variables.
  $vars = MapUtils::GetMapEntries(
      array('lineItemId' => new NumberValue($lineItemId)));

  // Create a statement to only select LICAs for the given lineItem ID.
  $filterStatement =
      new Statement("WHERE lineItemId = :lineItemId LIMIT 500", $vars);

  // Get LICAs by statement.
  $page = $licaService->getLineItemCreativeAssociationsByStatement(
      $filterStatement);

  // Display results.
  if (isset($page->results)) {
    $i = $page->startIndex;
    foreach ($page->results as $lica) {
      print $i . ') LICA with line item ID "' . $lica->lineItemId
          . '", creative ID "' . $lica->creativeId
          . '", and status "' . $lica->status
          . "\" was created.\n";
      $i++;
    }
  }

  print 'Number of results found: ' . $page->totalResultSetSize . "\n";
} catch (Exception $e) {
  print $e->getMessage() . "\n";
}

