<?php
ini_set('memory_limit','-1');
include_once('./gomi/wakame_no_youna_settei.php');
$font = __DIR__ .'/gomi/NotoSansCJKjp-Medium.otf';

$file = file_get_contents($argv[1]);

if($file === false){
	//파일 안맞으면 퉤
	echo "\e[91mうぇ！ ファイル名をちゃんとしろ。".PHP_EOL;
	goto owari;
}

$data = explode('----', $file);
$wakaran = explode("\n", $data[0]);

$type_n = explode('/', $wakaran[0]);
$type_c = explode('/', $wakaran[1]);

//종별 안맞으면 퉤
if(count($type_n) != count($type_c)){
	echo "\e[91mうぇ！ 種別は".count($type_n)."つなのに色が".count($type_c)."つ。".PHP_EOL;
	goto owari;
}

//종별구분부분 그리기
echo "\e[92m種別を描く:".PHP_EOL;
$syubetsubase = imagecreatetruecolor(100, 44*count($type_n));
$white = imagecolorallocate($syubetsubase, 255, 255, 255);
imagefill($syubetsubase, 0, 0, $white);

$i = 0;
foreach($type_n as & $value){
	echo $value.'('.$type_c[$i].')'.PHP_EOL;
	$type_c[$i] = explode(',', $type_c[$i]);
	$ttfbox = imagettfbbox(20, 0, $font, $value);
	if($ttfbox[4]+6 > 66){
		$syubetsuitem = imagecreatetruecolor($ttfbox[4]+6, 36);
		$color = imagecolorallocate($syubetsuitem, $type_c[$i][0], $type_c[$i][1], $type_c[$i][2]);
		imagefill($syubetsuitem, 0, 0, $color);
		imagettftext($syubetsuitem, 20, 0, 3, 28, $white, $font, $value);
		imagecopyresampled($syubetsubase, $syubetsuitem, 0, ($i*44)+4, 0, 0, 66, 36, $ttfbox[4]+6, 36);
	}
	else{
		$syubetsuitem = imagecreatetruecolor(66, 36);
		$color = imagecolorallocate($syubetsuitem, $type_c[$i][0], $type_c[$i][1], $type_c[$i][2]);
		imagefill($syubetsuitem, 0, 0, $color);
		imagettftext($syubetsuitem, 20, 0, 33-($ttfbox[4]*0.5), 28, $white, $font, $value);
		imagecopyresampled($syubetsubase, $syubetsuitem, 0, ($i*44)+4, 0, 0, 66, 36, 66, 36);
	}
	//imagepng($syubetsuitem, './neko/'.$i.'.png');
	
	$i = $i + 1;
}

imagepng($syubetsubase, './test.png');
/*
echo "\e[93m駅:".PHP_EOL;
$i = 0;
foreach($type_n as & $value){
	echo $value.'('.$type_c[$i].')'.PHP_EOL;
	$i = $i + 1;
}
*/
owari:
echo "\e[39m終わり！";

?>