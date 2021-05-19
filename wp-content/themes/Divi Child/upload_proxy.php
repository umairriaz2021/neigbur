<?php

echo "Upload proxy beginning...<br>\n";

$url = "http://apidev.snapd.com/v1/uploads/form";
$data = array('caption' => $_POST['caption'], 'type' => $_POST['type'], 'eventId' => $_POST['eventId']);



$filename = $_FILES['file']['name'];
$filedata = file_get_contents($_FILES['file']['tmp_name']);
$filesize = $_FILES['file']['size'];


/**
 * PHP's curl extension won't let you pass in strings as multipart file upload bodies; you
 * have to direct it at an existing file (either with deprecated @ syntax or the CURLFile
 * type). You can use php://temp to get around this for one file, but if you want to upload
 * multiple files then you've got a bit more work.
 *
 * This function manually constructs the multipart request body from strings and injects it
 * into the supplied curl handle, with no need to touch the file system.
 *
 * @param $ch resource curl handle
 * @param $boundary string a unique string to use for the each multipart boundary
 * @param $fields string[] fields to be sent as fields rather than files, as key-value pairs
 * @param $files string[] fields to be sent as files, as key-value pairs
 * @return resource the curl handle with request body, and content type set
 * @see http://stackoverflow.com/a/3086055/2476827 was what I used as the basis for this
 **/
function buildMultiPartRequest($ch, $boundary, $fields, $files) {
  global $auth, $accept;

    $delimiter = '-------------' . $boundary;
    $data = '';

    foreach ($fields as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
            . $content . "\r\n";
    }
    foreach ($files as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . "\r\n\r\n"
            . $content . "\r\n";
    }

    $data .= "--" . $delimiter . "--\r\n";

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
          $auth, $accept,
            'Content-Type: multipart/form-data; boundary=' . $delimiter,
            'Content-Length: ' . strlen($data)
        ],
        CURLOPT_POSTFIELDS => $data
    ]);

    return $ch;
}

// and here's how you'd use it



$auth = "Authorization: " . $_POST['Authorization'];
$accept = "Accept: application/vnd.pagerduty+json";


$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth, $accept));
//curl_setopt($ch, CURLOPT_URL, $url);
$ch = buildMultiPartRequest($ch, uniqid(),
    $_POST, [$filename => $filedata]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "$auth<br>\n";
echo "Response: $response<br>\n";

$log  = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . PHP_EOL .
        "Event ID: " . $data['eventId'] .PHP_EOL .
        "Authorization: " . $auth . PHP_EOL .
        "Response: " . $response . PHP_EOL .
        "-------------------------" . PHP_EOL;
//Save string to log, use FILE_APPEND to append.
file_put_contents('./logs/log_'.date("Y.j.n").'.log', $log, FILE_APPEND);

?>
