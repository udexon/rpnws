<?php


 $id = file_get_contents('php://input'); 

// $id = file_get_contents("j1"); 
// var_dump($id);

$logFile = "view.log";

// $id = $_POST['id'].'\n';
//$id = $_POST['id']."\n";

file_put_contents($logFile, $id."\n", FILE_APPEND);

$a=json_decode($id,true);
// RPML first command, need to access members of $a
// var_dump($a);

$b=json_decode($a[1][0],true);

// var_dump($b);

$n=$b[0][0];

$la['cmd']=array();
$lb=&$la['cmd'];

foreach($a as $k => $c) {
$lb[]=array($k, gettype($c));
// $lb[]=gettype($c);
}

// input output need to be string
// final output need to concatenate first output to second output
// or show only final output
// or let this be option?

// $a has current input from ajax
// $la is results of first command
// i.e. retrieve n lines from log

$lc = array();

foreach($a[1] as $k => $c) {

$lb[]=array($k,$c,gettype($c));
// $lb[]=gettype($c);

// put this is the loop, after matching first command
$j = json_decode($c, true);
// $n = $j[0][0];
$r = $j[0];

// 20170709 $r is [ arg cmd: ]
$l=count($j[0])-1;
$n=$j[0][$l];
$n1=$j[0][$l-1];

if ((int)($n)>0) { $la['log'] =  getline($n, $logFile);

$lc[$k] = 'log';
}
// This is output for first command
// How to pass to next command
// use index of array as indicator of output?
else if ($n=='g') {
$ki=$lc[$k-1];

// $la['grep'] = preg_grep( "/".$r[1]."/", $la['log']);

$la['grep'] = preg_grep( "/".$r[1]."/", $la[$ki]);

$lc[$k] = 'grep';
}
else if ($n=='dbg') $lc[$k] = 'dbg';

else if ($n=='l') {
$lc[$k] = 'l';
$la['l'] = getlength($logFile);
}
else if ($n=='b') {
$lc[$k] = 'b';
$la['b'] = getline($la['l'], $logFile);
}else if ($n=='t') {
$lc[$k] = 't';
$la['t'] = getline_top($la['l'], $logFile);
}

else if ($n=='c') {
$ki=$lc[$k-1];
$lc[$k] = 'c';
$la['c'] = count($la[$ki]);
}
else if ($n=='i') {
$ki=$lc[$k-1];
$lc[$k] = 'i';
$la['i'] = $la[$ki][$n1];
// $n1;
// $r; // view $r to debug

 // count($la[$ki]);
}
else if ($n=='k') {
$ki=$lc[$k-1];
$lc[$k] = 'k';
$la['k'] = array_keys(($la[$ki])); //[$n1]; cannot trim if not string
// $r; // view $r to debug

 // count($la[$ki]);
}
else if ($n=='gt') {
$ki=$lc[$k-1];
$lc[$k] = 'gt';
$la['gt'] = gettype($la[$ki]); 
//[$n1];
// $r; // view $r to debug

 // count($la[$ki]);
}
else if ($n=='d') {
$ki=$lc[$k-1];
$lc[$k] = 'd';
$la['d'] = json_decode($la[$ki],true); 
}
else if ($n=='snl') {
$ki=$lc[$k-1];
$lc[$k] = 'snl'; // explode or split on \n
$la['snl'] = explode("\n", $la[$ki]); // works without escape!! [ 20170711 0235 ]
// $la['snl'] = ($la[$ki]);  
file_put_contents("o_snl_d", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la['snl'])."\n", FILE_APPEND);
file_put_contents("o_snl_i", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la[$ki])."\n", FILE_APPEND);
}
else if ($n=='ss') {
$ki=$lc[$k-1];
$lc[$k] = 'ss'; // explode or split on \n
$la['ss'] = stripslashes($la[$ki]);
file_put_contents("o_ss_d", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la['ss'])."\n", FILE_APPEND);
file_put_contents("o_ss_i", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la[$ki])."\n", FILE_APPEND);
}
else if ($n=='sss') {
$ki=$lc[$k-1];
$lc[$k] = 'sss'; // explode or split on \n
$la['sss'] = explode("\n", $la[$ki]); // works without escape!! [ 20170711 0235 ]
// $la['snl'] = ($la[$ki]);  
file_put_contents("o_sss_d", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la['snl'])."\n", FILE_APPEND);
file_put_contents("o_sss_i", "\n\n".date("Y-m-d H:i:s")."> ".json_encode($la[$ki])."\n", FILE_APPEND);
}
else if ($n=='nc') {
$ki=$lc[$k-1];
$lc[$k] = 'nc'; // no comments
$la['nc'] = preg_grep("|//|", $la[$ki], PREG_GREP_INVERT); 
}
else if ($n=='j') {
$ki=$lc[$k-1];
$lc[$k] = 'j'; // join lines
$la['j'] = implode($la[$ki]); 
}
else if ($n=='<') break;
// <: front end post processing flag


// $lb[]=array($j,count($j));
// $lb[]=array($j[0],count($j[0]));

}

// $la is used to store input output between commands
// Sections of $la may be deleted for shorter final output
// How to control options for final output?

$la['seq'] = $lc;
$cc = count($lc)-1;
$kl = $lc[$cc];

// dbg: debug mode, outputs all
// else outputs last result
if ($kl=='dbg') $lf=$la;
else $lf = $la[$kl];

// echo date("Y-m-d H:i:s")."> ".$a[0]." _ ".$a[1][0]." _ ". (is_int($n)?"int":"ltr"). " <p>" . sprintf("%d %s
// echo date("Y-m-d H:i:s")." _> ".$a[0]." _> ".$a[1][0] . " _> " . sprintf("%d %s", (int)($n)). json_encode($lf);
echo date("Y-m-d H:i:s")." _: ".$a[0]." _: ".$a[1][0] . " _: " . json_encode($lf);


// count($b)." ".

file_put_contents("o_tmp", json_encode($lf));

function getline($n, $f)
{
$a=file($f);
$l=count($a);

$b=array();

for($i=0; $i<$n; $i++)
$b[]=$a[$l-$i-1];

return $b;
}

function getline_top($n, $f)
{
$a=file($f);
$l=count($a);

$b=array();

for($i=0; $i<$n; $i++)
$b[]=$a[$i];

return $b;
}

function getlength( $f )
{
$a=file($f);
$l=count($a);

return $l;
}

?>
