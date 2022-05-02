<?php
/*@website: https://www.ludo.one/php-instagram-auto-post.html */
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");


function removeaccents($str, $charset='utf-8'){
$str = htmlentities($str, ENT_NOQUOTES, $charset);
$str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
$str = preg_replace('#&[^;]+;#', '', $str);
return $str;
}


function SendRequest($url, $post, $data, $userAgent, $cookies) {
 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/'.$url);
 curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


if($post) {
 curl_setopt($ch, CURLOPT_POST, true);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
 }
 
 if($cookies) {
 curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); 
 } else {
 curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
 }
 
 $response = curl_exec($ch);
 $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 curl_close($ch);
 
 return [
 'code' => $http, 
 'response' => $response,
 ];
}


function GenerateGuid() {
 return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
 mt_rand(0, 65535), 
 mt_rand(0, 65535), 
 mt_rand(0, 65535), 
 mt_rand(16384, 20479), 
 mt_rand(32768, 49151), 
 mt_rand(0, 65535), 
 mt_rand(0, 65535), 
 mt_rand(0, 65535));
}


function GenerateUserAgent() { 
 $resolutions = ['720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320'];
 $versions = ['GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100'];
 $dpis = ['120', '160', '320', '240'];


$ver = $versions[array_rand($versions)];
 $dpi = $dpis[array_rand($dpis)];
 $res = $resolutions[array_rand($resolutions)];
 
 return 'Instagram 4.'.mt_rand(1,2).'.'.mt_rand(0,2).' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi.'; '.$res.'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
}


function GenerateSignature($data) {
 return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
}


function GetPostData($filename) {
 if(!$filename) {
 echo "The image doesn't exist ".$filename;
 } else {
 $data = [
 'device_timestamp' => time(), 
 'photo' => '@'.$filename
 ];
 return $data;
 }
}


$fichier = "http://www.your-website.com/feed.rss";


$dom = new DOMDocument();
if (!$dom->load($fichier)) {die('Error XML');}


$itemList = $dom->getElementsByTagName("item");
$nbitem = $itemList ->length;


$nb_min = 1;
$nb_max = $nbitem;
$nombre = mt_rand($nb_min,$nb_max);


$countos=0;
foreach ($itemList as $item) {


$countos++;
if($countos==$nombre){
$titos = $item->getElementsByTagName('title');
if ($titos->length > 0) {$biztitos = $titos->item(0)->nodeValue;



}
$link = $item->getElementsByTagName('link');
if ($link->length > 0) {$bizlink = $link->item(0)->nodeValue;
}
 
$pic = $item->getElementsByTagName('description');
if ($pic->length > 0) {$bizpic = $pic->item(0)->nodeValue;


$html = $bizpic;


$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXPath($doc);
$src = $xpath->evaluate("string(//img/@src)");


$bizurlb=removeaccents($biztitos);
$bizurlb = substr($bizurlb, 0, -1);
$bizurlb=strtolower($bizurlb);
$bizurlb=preg_replace("/ /","-",$bizurlb);
$bizurlb=preg_replace("/\"/","",$bizurlb);
$bizurlb=preg_replace("/,/","",$bizurlb);
$bizurlb=preg_replace("/'/","",$bizurlb);
$bizurlb=preg_replace("/:/","",$bizurlb);
$bizurlb=preg_replace("/°/","-",$bizurlb);
$bizurlb=preg_replace("/%/","-",$bizurlb);
$bizurlb=preg_replace("/&/","\|",$bizurlb);
$bizurlb=preg_replace("/---/","-",$bizurlb);
$bizurlb=preg_replace("/--/","-",$bizurlb);


if (!file_exists('img/'.$bizurlb.'_458x458.jpg')){
copy($src,'img/'.$bizurlb.'_458x458.jpg');
}
}


}
}


//**********************************
$file = './img/'.$bizurlb.'_458x458.jpg' ;


$x = 400;


$y = 400;


$size = getimagesize($file);


if ( $size) {


if ($size['mime']=='image/jpeg' ) {


$img_big = imagecreatefromjpeg($file);


$img_new = imagecreate($x, $y);


$img_mini = imagecreatetruecolor($x, $y)


or $img_mini = imagecreate($x, $y);



imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);


imagejpeg($img_mini,$file );


}


elseif ($size['mime']=='image/png' ) {


$img_big = imagecreatefrompng($file);


$img_new = imagecreate($x, $y);


$img_mini = imagecreatetruecolor($x, $y)


or $img_mini = imagecreate($x, $y);


imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);


imagepng($img_mini,$file );


}


elseif ($size['mime']=='image/gif' ) {


$img_big = imagecreatefromgif($file);


$img_new = imagecreate($x, $y);


$img_mini = imagecreatetruecolor($x, $y)


or $img_mini = imagecreate($x, $y);


imagecopyresized($img_mini,$img_big,0,0,0,0,$x,$y,$size[0],$size[1]);


imagegif($img_mini,$file );


}


}



 // Set the username and password of the account that you wish to post a photo to
$username = 'username@email.com';
$password = 'your-instagram-password';


// Set the path to the file that you wish to post.
// This must be jpeg format and it must be a perfect square
$filename = './img/'.$bizurlb.'_458x458.jpg';


// Set the caption for the photo
$caption = $biztitos.' : '.$bizlink;


// Define the user agent
$agent = GenerateUserAgent();


// Define the GuID
$guid = GenerateGuid();


// Set the devide ID
$device_id = "android-".$guid;


/* LOG IN */
// You must be logged in to the account that you wish to post a photo too
// Set all of the parameters in the string, and then sign it with their API key using SHA-256
$data = '{"device_id":"'.$device_id.'","guid":"'.$guid.'","username":"'.$username.'","password":"'.$password.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
$sig = GenerateSignature($data);
$data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=4';
$login = SendRequest('accounts/login/', true, $data, $agent, false);


if(strpos($login['response'], "Sorry, an error occurred while processing this request.")) {
 echo "Request failed, there's a chance that this proxy/ip is blocked";
} else { 
 if($login['code'] != 200) {
 echo "Error while trying to login";
 } else { 
 // Decode the array that is returned
 $obj = @json_decode($login['response'], true);


if(empty($obj)) {
 echo "Could not decode the response: ".$body;
 } else {
 // Post the picture
 $data = GetPostData($filename);
 $post = SendRequest('media/upload/', true, $data, $agent, true); 
 
 if($post['code'] != 200) {
 echo "Error while trying to post the image";
 } else {
 // Decode the response 
 $obj = @json_decode($post['response'], true);


if(empty($obj)) {
 echo "Could not decode the response";
 } else {
 $status = $obj['status'];


if($status == 'ok') {
 // Remove and line breaks from the caption
 $caption = preg_replace("/\r|\n/", "", $caption);


$media_id = $obj['media_id'];
 $device_id = "android-".$guid;
 $data = '{"device_id":"'.$device_id.'","guid":"'.$guid.'","media_id":"'.$media_id.'","caption":"'.trim($caption).'","device_timestamp":"'.time().'","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'; 
 $sig = GenerateSignature($data);
 $new_data = 'signed_body='.$sig.'.'.urlencode($data).'&ig_sig_key_version=4';


// Now, configure the photo
 $conf = SendRequest('media/configure/', true, $new_data, $agent, true);
 
 if($conf['code'] != 200) {
 echo "Error while trying to configure the image";
 } else {
 if(strpos($conf['response'], "login_required")) {
 echo "You are not logged in. There's a chance that the account is banned";
 } else {
 $obj = @json_decode($conf['response'], true);
 $status = $obj['status'];


if($status != 'fail') {
 echo "Success";
 } else {
 echo 'Fail';
 }
 }
 }
 } else {
 echo "Status isn't okay";
 }
 }
 }
 }
 }
}
 
?>


