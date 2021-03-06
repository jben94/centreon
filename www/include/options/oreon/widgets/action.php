<?php
/**
 * Copyright 2005-2015 Centreon
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give Centreon
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of Centreon choice, provided that
 * Centreon also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

require_once realpath(dirname(__FILE__) . "/../../../../../config/centreon.config.php");
require_once _CENTREON_PATH_ . "/bootstrap.php";
require_once _CENTREON_PATH_ . "www/class/centreon.class.php";
require_once _CENTREON_PATH_ . "www/class/centreonXML.class.php";
require_once _CENTREON_PATH_ . "www/class/centreonSession.class.php";
require_once _CENTREON_PATH_ . "www/class/centreonDB.class.php";
require_once _CENTREON_PATH_ . "www/class/centreonWidget.class.php";

session_start();

require_once realpath(dirname(__FILE__) . "/../../../../../bootstrap.php");

$factoryUtils = new \CentreonLegacy\Core\Utils\Factory($dependencyInjector);
$utils = $factoryUtils->newUtils();

$factory = new \CentreonLegacy\Core\Widget\Factory($dependencyInjector, $utils);
$widgetInfoObj = $factory->newInformation();

$xml = new CentreonXML();
$xml->startElement('response');
try {
    if (!isset($_SESSION['centreon']) ||
        !isset($_REQUEST['action']) ||
        !isset($_REQUEST['directory'])) {
            throw new Exception('Error with request');
    }
    $centreon = $_SESSION['centreon'];
    $action = $_REQUEST['action'];
    $directory = $_REQUEST['directory'];
    $db = new CentreonDB();
    $widgetObj = new CentreonWidget($centreon, $db);
    switch ($action) {
        case 'install':
            $widgetInstaller = $factory->newInstaller($directory);
            $widgetInstaller->install();
            break;
        case 'install-all':
            $widgetsToInstall = $widgetInfoObj->getInstallableList();

            foreach ($widgetsToInstall as $widgetName => $widgetData) {
                $widgetInstaller = $factory->newInstaller($widgetName);
                $widgetInstaller->install();
            }
            break;
        case 'uninstall':
            $widgetRemover = $factory->newRemover($directory);
            $widgetRemover->remove();
            break;
        case 'upgrade':
            $widgetUpgrader = $factory->newUpgrader($directory);
            $widgetUpgrader->upgrade();
            break;
        case 'upgrade-all':
            $widgetsToUpgrade = $widgetInfoObj->getUpgradeableList();

            foreach ($widgetsToUpgrade as $widgetName => $widgetData) {
                $widgetUpgrader = $factory->newUpgrader($widgetName);
                $widgetUpgrader->upgrade();
            }
            break;
        default:
            throw new Exception('Unknown action');
    }
    $xml->writeElement('result', 1);
} catch (Exception $e) {
    $xml->writeElement('error', $e->getMessage());
}
$xml->endElement();
header('Content-Type: text/xml');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: no-cache, must-revalidate');
$xml->output();
