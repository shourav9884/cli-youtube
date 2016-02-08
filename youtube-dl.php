<?php
function curlGet($URL) {
	global $config; // get global $config to know if $config['multipleIPs'] is true
    $ch = curl_init();
    $timeout = 3;
   
    curl_setopt( $ch , CURLOPT_URL , $URL );
    curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
    curl_setopt( $ch , CURLOPT_CONNECTTIMEOUT , $timeout );
	
    $tmp = curl_exec( $ch );
    curl_close( $ch );
    return $tmp;
}
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . '' . $units[$pow]; 
} 
function get_size($url) {
	
	$my_ch = curl_init();
	
	curl_setopt($my_ch, CURLOPT_URL,$url);
	curl_setopt($my_ch, CURLOPT_HEADER,         true);
	curl_setopt($my_ch, CURLOPT_NOBODY,         true);
	curl_setopt($my_ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($my_ch, CURLOPT_TIMEOUT,        10);
	$r = curl_exec($my_ch);
	 foreach(explode("\n", $r) as $header) {
		if(strpos($header, 'Content-Length:') === 0) {
			return trim(substr($header,16)); 
		}
	 }
	return '';
}
$video_id="";
$line = readline("YouTube Video ID: ");

$video_id = $line;
$url = 'http://www.youtube.com/get_video_info?&video_id='. $video_id.'&asv=3&el=detailpage&hl=en_US';
$curl_result=curlGet($url);
$result_info = array();

// fwrite(handle, string)
parse_str($curl_result,$result_info);

if($result_info['status']=="ok")
{
	echo "\nVideo Info:\n\nTitle: ".$result_info['title'];
	echo "\nAuthor:".$result_info['author'];
	echo "\nDuration:".floor($result_info['length_seconds']/60)." minutes ".($result_info['length_seconds']%60)." Seconds\n\n";
	echo "\nVideo Types:\n";

	$video_types=array();
	$my_formats_array = explode(',',$result_info['url_encoded_fmt_stream_map']);
	$i=0;
	$sig="";
	foreach($my_formats_array as $format) {
		parse_str($format);
		$avail_formats[$i]['itag'] = $itag;
		$avail_formats[$i]['quality'] = $quality;
		$type = explode(';',$type);
		$avail_formats[$i]['type'] = $type[0];
		$avail_formats[$i]['url'] = urldecode($url) . '&signature=' . $sig;
		parse_str(urldecode($url));
		// $avail_formats[$i]['expires'] = date("G:i:s T", $expire);
		$avail_formats[$i]['ipbits'] = $ipbits;
		// $avail_formats[$i]['ip'] = $ip;
		$video_types[]=$avail_formats[$i];

		$i++;
	}
	$i=1;
	foreach ($video_types as $type) {
		echo "id: ".$i." : Quality => ".$type['quality']." Size => ".formatBytes(get_size($type['url']))." Type => ".$type['type']." \n";
		$i++;
	}
	echo "\n\n";
	$choose_id=readline("Choose 1-".($i-1).": ");
	$choose_id = intval($choose_id);
	if($choose_id>0 && $choose_id<$i)
	{

	}
	else
	{
		while (true) {
			echo "\nWrong choice.\n";
			$choose_id=readline("Choose 1-".($i-1).": ");
			$choose_id = intval($choose_id);
			if($choose_id>0 && $choose_id<$i)
				break;
		}
	}
	$dl_url = $video_types[$choose_id-1]['url'];
	echo $dl_url;
	$url_info = array();
	parse_str($dl_url,$url_info);
	$mime = filter_var($url_info['mime']);
	$ext  = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
	$file_name = $result_info['title'].".".$ext;
	echo "\n\n";
	$folder_name = readline("Enter directory or skip to download in current folder:");
	$output = exec("wget -O '".$folder_name.$file_name."' '".$dl_url."'");
	var_dump($output);
}
else 
	echo "invalid video id";

// wget -O "test.webm" "http://r3---sn-x5guiuxaxjvh-q5jl.googlevideo.com/videoplayback?initcwndbps=1182500&sver=3&mv=m&gcr=bd&ms=au&id=o-ADPyeQFIQG927Xy42Sx-rAyXz_BZg1OqXEJx9nYLHjM5&pl=22&source=youtube&pcm2cms=yes&dur=0.000&ip=103.60.172.10&key=yt6&mn=sn-x5guiuxaxjvh-q5jl&signature=58B71A4C3E44982682C6F134E203C8D301E51724.0747BECBE25C4E85F5179A717A28199114170A11&mm=31&itag=43&upn=Tio3fQX7BsQ&mt=1454945043&fexp=9416126,9420452,9422596,9423661,9423662&sparams=dur,gcr,id,initcwndbps,ip,ipbits,itag,lmt,mime,mm,mn,ms,mv,pcm2cms,pl,ratebypass,source,upn,expire&expire=1454966798&mime=video/webm&lmt=1444920228355676&ipbits=0&ratebypass=yes&signature="