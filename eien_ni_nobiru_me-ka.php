<?php
ini_set('memory_limit','-1');
include_once('./gomi/wakame_no_youna_settei.php');
putenv('GDFONTPATH=' . realpath('.'));
$font = './gomi/NotoSansCJKjp-Medium.otf';
//echo __DIR__ .'/gomi/NotoSansCJKjp-Medium.otf'.PHP_EOL;

$file = file_get_contents($argv[1]);

if($file === false){
	//파일 안맞으면 퉤
	echo "\e[91mうぇ！ ファイル名をちゃんとしろ。".PHP_EOL;
	goto owari;
}

$data = explode('-----', $file);
$type = explode("\n", $data[0]);
$station = array_filter(array_map("trim", explode("\n", $data[1])));

$type_n = explode('/', $type[0]);
$type_c = explode('/', $type[1]);

//종별 안맞으면 퉤
if(count($type_n) != count($type_c)){
	echo "\e[91mうぇ！ 種別は".count($type_n)."つなのに色が".count($type_c)."つ。".PHP_EOL;
	goto owari;
}

//종별구분부분 그리기
echo "\e[92m種別を描く:".PHP_EOL;
$imagebase = imagecreatetruecolor(87+($eki_no_aida*count($station)), 250+(32*count($type_n)));
$white = imagecolorallocate($imagebase, 255, 255, 255);
imagefill($imagebase, 0, 0, $white);

$i = 0;
foreach($type_n as & $value){
	echo $value.'('.$type_c[$i].')'.PHP_EOL;
	$color = explode(',', $type_c[$i]);
	$ttfbox = imagettfbbox(18, 0, $font, $value);
	if($ttfbox[4]+6 > 66){
		$item = imagecreatetruecolor($ttfbox[4]+6, 34);
		$color = imagecolorallocate($item, $color[0], $color[1], $color[2]);
		imagefill($item, 0, 0, $white);
		imagettftext($item, 18, 0, 3, 28, $color, $font, $value);
		imagecopyresampled($imagebase, $item, 5, ($i*32)+7, 0, 0, 66, 34, $ttfbox[4]+6, 34);
	}
	else{
		$item = imagecreatetruecolor(66, 34);
		$color = imagecolorallocate($item, (int)$color[0], (int)$color[1], (int)$color[2]);
		imagefill($item, 0, 0, $white);
		imagettftext($item, 18, 0, 33-($ttfbox[4]*0.5), 28, $color, $font, $value);
		imagecopy($imagebase, $item, 5, ($i*32)+7, 0, 0, 66, 34);
	}
	imagedestroy($item);
	$i = $i + 1;
}

//역 그리기
echo "\e[93m駅:".PHP_EOL;
$i = 0;
foreach($station as & $value){
	//var_dump($station);
	$value = explode('/', $value);
	echo $value[0].PHP_EOL;
	//종별 선긋기
	$i2 = 0;
	foreach($type_c as & $type){
		$item = imagecreatetruecolor($eki_no_aida, 22);
		$color = explode(',', $type);
		$color = imagecolorallocate($item, (int)$color[0], (int)$color[1], (int)$color[2]);
		imagefill($item, 0, 0, $color);
		if($value[$i2+1]){
			imagettftext($item, 17, 0, ($eki_no_aida*0.5)-10, 20, $white, $font, '●');
		}
		imagecopy($imagebase, $item, 73+($eki_no_aida*$i), ($i2*32)+15, 0, 0, $eki_no_aida, 22);
		imagedestroy($item);
		$i2 = $i2 + 1;
	}
	//글자적기
	$name = preg_split('//u', $value[0], null, PREG_SPLIT_NO_EMPTY);
	$item = imagecreatetruecolor($eki_no_aida, 500);
	$color = imagecolorallocate($item, 0, 0, 0);
	imagefill($item, 0, 0, $white);
	$h = -($ekimei_dekasa*0.192);
	foreach($name as & $char){
		$ttfbox = imagettfbbox($ekimei_dekasa, 0, $font, $char);
		$h = ($ekimei_dekasa*0.192) + $h + ($ttfbox[3]-$ttfbox[5]);
		imagettftext($item, $ekimei_dekasa, 0, ($eki_no_aida*0.5)-($ekimei_dekasa*0.58), $h, $color, $font, $char);
	}
	if(230 < $h)
		imagecopyresampled($imagebase, $item, 73+($eki_no_aida*$i), ($i2*32)+15, 0, 0, $eki_no_aida, 230, $eki_no_aida, $h+5);
	else
		imagecopy($imagebase, $item, 73+($eki_no_aida*$i), ($i2*32)+15, 0, 0, $eki_no_aida, 500);
	imagedestroy($item);
	
	$i = $i + 1;
}

imagepng($imagebase, './output.png');

owari:
echo "\e[97m終わり！\e[39m";

?>