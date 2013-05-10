<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once '../google-api-php-client/src/Google_Client.php';
require_once '../google-api-php-client/src/contrib/Google_DriveService.php';
require_once '../google-api-php-client//src/contrib/Google_Oauth2Service.php';

// load Zend Gdata libraries
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');


session_start();
//session_destroy();die;
$client = new Google_Client();
$client->setApplicationName("Google+ PHP Starter Application");
// Visit https://code.google.com/apis/console to generate your
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$client->setClientId('571777521210.apps.googleusercontent.com');
$client->setClientSecret('4hCZ-Azr_QlR9zr1Xux0_Fhb');
$client->setRedirectUri('http://localhost/google_spreadsheet/index.php');

$client->setScopes(array('https://spreadsheets.google.com/feeds', 'https://www.googleapis.com/auth/drive',));
$plus = new Google_Oauth2Service($client);
$service = new Google_DriveService($client);

function insertFile($service, $title, $description, $parentId, $mimeType, $filename) {
    $file = new Google_DriveFile();
    $file->setTitle($title);
    $file->setDescription($description);
    $file->setMimeType($mimeType);

    // Set the parent folder.
    if ($parentId != null) {
        $parent = new ParentReference();
        $parent->setId($parentId);
        $file->setParents(array($parent));
    }

    try {
        $data = $filename; //file_get_contents($filename);

        $createdFile = $service->files->insert($file, array(
            'data' => $data,
            'mimeType' => $mimeType,
                ));

        // Uncomment the following line to print the File ID
        // print 'File ID: %s' % $createdFile->getId();

        return $createdFile;
    } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
    }
}

if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);

    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
    $tok = json_decode($client->getAccessToken());
    //echo $tok->access_token;
    $authsub = Zend_Gdata_AuthSub::getHttpClient($tok->access_token);
    $spreadsheet_service = new Zend_Gdata_Spreadsheets($authsub);
    $feed = $spreadsheet_service->getSpreadsheetFeed();


    try {

        /* $filename = "";
          $title = "sanjeevtest";
          $description = "hello test";
          $parentId = null;
          $mimeType = "application/vnd.google-apps.spreadsheet";
          $file = insertFile($service, $title, $description, $parentId, $mimeType, $filename);
          //var_dump($file);
          $fid = $file['id'];


          // get spreadsheet entry
          $ssEntry = $spreadsheet_service->getSpreadsheetEntry(
          'https://spreadsheets.google.com/feeds/spreadsheets/' . $fid);

          // get worksheet feed for this spreadsheet
          $wsFeed = $spreadsheet_service->getWorksheetFeed($ssEntry);
          // create new entry
          $doc = new DOMDocument();
          $doc->formatOutput = true;
          $entry = $doc->createElement('atom:entry');
          $entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
          $entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gs', 'http://schemas.google.com/spreadsheets/2006');
          $doc->appendChild($entry);

          // add title, row and column counts
          $title = $doc->createElement('atom:title', 'Jan 2011');
          $entry->appendChild($title);
          $rows = $doc->createElement('gs:rowCount', '10');
          $entry->appendChild($rows);
          $cols = $doc->createElement('gs:colCount', '10');
          $entry->appendChild($cols);

          // insert entry
          $entryResult = $spreadsheet_service->insertEntry($doc->saveXML(), $wsFeed->getLink('self')->getHref());
          //echo 'The ID of the new worksheet entry is: ' . $entryResult->id; */
        // set target spreadsheet and worksheet
        $ssKey = '0AjgekfOLyBPedDRaLUhwYTZobTFJY09fa082VDA5dmc';
        //$wsKey = $entryResult->id;
//var_dump($entryResult);
        // create row content
        $row = array(
            "date" => "24-12-2010",
            "task" => "Server reconfiguration",
            "hours" => "3.5"
        );

        // insert new row
        $entryRow = $spreadsheet_service->insertRow($row, $ssKey,1);
        //echo 'The ID of the new row entry is: ' .$entryRow->id ;
    } catch (Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
} else {
    $authUrl = $client->createAuthUrl();
}
?>

<div class="box">


    <?php
    if (isset($authUrl)) {
        print "<a class='login' href='$authUrl'>Create Event</a>";
    } else {
        print "<a class='logout' href='?logout'>Logout</a>";
    }
    ?>
    <!--<ul>
    <?php //foreach ($feed as $entry): ?>
            <li class="name"><?php //echo $entry->getTitle();                   ?></li>
    <?php //endforeach; ?>
</ul>-->
</div>