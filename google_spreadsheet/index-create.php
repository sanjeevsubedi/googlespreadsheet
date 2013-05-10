<?php

require_once '../google-api-php-client/src/Google_Client.php';
require_once '../google-api-php-client/src/contrib/Google_DriveService.php';

$client = new Google_Client();
// Get your credentials from the APIs Console
$client->setClientId('571777521210.apps.googleusercontent.com');
$client->setClientSecret('4hCZ-Azr_QlR9zr1Xux0_Fhb');
$client->setRedirectUri('http://localhost/google_spreadsheet/index.php');
$client->setScopes(array('https://www.googleapis.com/auth/drive'));

$service = new Google_DriveService($client);

//$authUrl = $client->createAuthUrl();
//Request authorization
$authCode = isset($_GET['code']) ? $_GET['code'] : '';


// Exchange authorization code for access token
$accessToken = $client->authenticate($authCode);
$client->setAccessToken($accessToken);

/**
 * Insert new file.
 *
 * @param Google_DriveService $service Drive API service instance.
 * @param string $title Title of the file to insert, including the extension.
 * @param string $description Description of the file to insert.
 * @param string $parentId Parent folder's ID.
 * @param string $mimeType MIME type of the file to insert.
 * @param string $filename Filename of the file to insert.
 * @return Google_DriveFile The file that was inserted. NULL is returned if an API error occurred.
 */
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

$filename = "";
$title = "sanjeevtest";
$description = "hello test";
$parentId = null;
$mimeType = "application/vnd.google-apps.spreadsheet";
$file = insertFile($service, $title, $description, $parentId, $mimeType, $filename);
var_dump($file);
//Insert a file
/* $file = new Google_DriveFile();
  $file->setTitle('charlie.txt');
  $file->setDescription('A test document');
  $file->setMimeType('text/plain');

  $data = file_get_contents('test.txt');

  $createdFile = $service->files->insert($file, array(
  'data' => $data,
  'mimeType' => 'text/plain',
  ));

  print_r($createdFile); */
?>

