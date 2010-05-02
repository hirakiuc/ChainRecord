<?php
/**
 * Here is a sample file that demonstrates all of PEAR_PackageFileManager2's features.
 *
 * First, a subpackage is created that is then automatically processed with the parent package
 * Next, the parent package is created.  Finally, a compatible PEAR_PackageFileManager object is
 * automatically created from the parent package in order to maintain two copies of the same file.
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR_PackageFileManager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: makepackage.php 213477 2006-05-21 17:13:08Z farell $
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.6.0
 * @ignore
 */
/**
 * This is the only setup function needed
 */
require_once 'PEAR/PackageFileManager2.php';
// recommended - makes PEAR_Errors act like exceptions (kind of)
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$packagexml = new PEAR_PackageFileManager2();
$packagexml->setOptions(array('filelistgenerator' => 'file',
      'packagedirectory' => dirname(__FILE__)."/src",
      'baseinstalldir' => 'PEAR',
      'simpleoutput' => true));
$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setPackage('ChainRecord');
$packagexml->setChannel('__uri');
$packagexml->setReleaseVersion('0.1.0');
$packagexml->setAPIVersion('0.1.0');
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setSummary('Object-Relation Mapper for php with Method Chain.');
$packagexml->setDescription('Database Access Library for php 5.2.x');
$packagexml->setNotes('Initial release');
$packagexml->setPhpDep('5.2.0');
$packagexml->setPearinstallerDep('1.4.0a12');
$packagexml->addPackageDepWithChannel('required', 'PEAR_PackageFileManager', 'pear.php.net', '1.5.1');
$packagexml->addMaintainer('lead', 'hirakiuc', 'Daisuke Hirakiuchi', 'hirakiuc@gmail.com');
$packagexml->setLicense('MIT License', 'http://www.opensource.org/licenses/mit-license.php');
$packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
//$pkg = &$packagexml->exportCompatiblePackageFile1();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
//    $pkg->writePackageFile();
    $packagexml->writePackageFile();
} else {
//    $pkg->debugPackageFile();
    $packagexml->debugPackageFile();
}
?>
