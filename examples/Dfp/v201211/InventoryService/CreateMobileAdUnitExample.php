<?php
/**
 * This example creates a new mobile ad unit under a the effective root ad unit.
 * Mobile features need to be enabled on your account to use mobile targeting.
 * To determine which ad units exist, run GetInventoryTreeExample.php or
 * GetAllAdUnitsExample.php.
 *
 * Tags: NetworkService.getCurrentNetwork
 * Tags: InventoryService.createAdUnit
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
 * @subpackage v201211
 * @category   WebServices
 * @copyright  2012, Google Inc. All Rights Reserved.
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License,
 *             Version 2.0
 * @author     Vincent Tsao
 */
error_reporting(E_STRICT | E_ALL);

// You can set the include path to src directory or reference
// DfpUser.php directly via require_once.
// $path = '/path/to/dfp_api_php_lib/src';
$path = dirname(__FILE__) . '/../../../../src';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Google/Api/Ads/Dfp/Lib/DfpUser.php';

try {
  // Get DfpUser from credentials in "../auth.ini"
  // relative to the DfpUser.php file's directory.
  $user = new DfpUser();

  // Log SOAP XML request and response.
  $user->LogDefaults();

  // Get the InventoryService.
  $inventoryService = $user->GetService('InventoryService', 'v201211');

  // Get the NetworkService.
  $networkService = $user->GetService('NetworkService', 'v201211');

  // Get the effective root ad unit's ID for all ad units to be created under.
  $network = $networkService->getCurrentNetwork();
  $effectiveRootAdUnitId = $network->effectiveRootAdUnitId;

  $adUnit = new AdUnit();
  $adUnit->name = uniqid('Mobile_Ad_Unit_');
  $adUnit->parentId = $effectiveRootAdUnitId;
  $adUnit->description = 'Ad unit description.';
  $adUnit->targetWindow = 'BLANK';
  $adUnit->targetPlatform = 'MOBILE';
  $adUnit->mobilePlatform = 'APPLICATION';

  // Create ad unit size.
  $adUnitSize = new AdUnitSize();
  $adUnitSize->size = new Size(400, 300, FALSE);
  $adUnitSize->environmentType = 'BROWSER';

  // Set the size of possible creatives that can match this ad unit.
  $adUnit->adUnitSizes = array($adUnitSize);

  // Create the ad unit on the server.
  $adUnit = $inventoryService->createAdUnit($adUnit);

  // Display results.
  if (isset($adUnit)) {
    printf("An ad unit with ID '%s' was created under parent with ID '%s'.\n",
        $adUnit->id, $adUnit->parentId);
  } else {
    print "No ad unit was created.\n";
  }
} catch (Exception $e) {
  print $e->getMessage() . "\n";
}

