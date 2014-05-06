<?php
set_time_limit(0);
$server = "irc.freenode.org";
$nick = "etc-bot";
$channels = array("#goeosbottest", "#dchatt","#atxhack","#anonbottest",/*"#jailbreakqa"*/"#wenetapls");
$port = 6667;
$lastused = array();
$connection = fsockopen("$server", $port);
fputs ($connection, "USER $nick $nick $nick $nick :$nick\n");//lulz
fputs ($connection, "NICK $nick\n");
foreach($channels as $channel)
{
fputs ($connection, "JOIN {$channel}\n");
}
while(1){
	while($data = fgets($connection)){
	echo $data;
	flush();

	$a1 = explode(' ', $data);
	$a2 = explode(':', $a1[3]);
	$a3 = explode('@', $a1[0]);
	$a4 = explode('!', $a3[0]);
	$a5 = explode(':', $a4[0]);
	$a6 = explode(':', $data);
	$onlyword = substr($a1[4], 0, -1);
	$user = $a5[1];
	$inchannel = $a1[2];
	$firstword = $a1[4];
	if($a1[0] == "PING"){
		fputs($connection, "PONG ".$a1[1]."\n");
	}
	$args = NULL; for ($i = 4; $i < count($a1); $i++) {$args .= $a1[$i] . ' ';}
	$all = substr($args, 0, -1);

	$len = strlen($firstword) + 1;
	$argsafterfirstword = substr($all,$len);
if(strpos(substr(strtolower($a1[3]),1),"!stether") !== false )
{
fputs($connection,"PRIVMSG {$inchannel} :{$user}: Add repo.natur-kultur.eu to your sources in cydia and install 7.1 semitether.\n");
}
if(strpos(substr(strtolower($a1[3]),1),"!karma") !== false )
{
print_r($lastused);
if(isset($lastused[$user]))
{
$aa = time()-$lastused[$user];
}
echo "aa = $aa\n";
if(($aa > 10) || (!isset($lastused[$user])))
{
echo "valid\n";
$lastused[$user] = time();
echo "setting timer...\n";
$t = $a1[4];
if(isset($a1[5]))
{
$t .= " " . $a1[5];
}
$ruser = strtolower(str_replace("\n","",str_replace("\r","",$t)));
$uurl = url($ruser,ge("http://www.jailbreakqa.com/users/?q=" . urlencode($ruser)));
if(!$uurl)
{
  fputs($connection,"PRIVMSG {$inchannel} :{$user}: User not found.\n");
}
else
{
  fputs($connection,"PRIVMSG {$inchannel} :{$user}: {$ruser} has " . karma($uurl) . " karma.\n");
}
echo "done with request\n";
}
}
if(strpos($data, $nick)!== false && ((strpos($data, 'thx')!== false) || (strpos($data, 'thank')!== false)|| (strpos($data, 'thanx')!== false))){
fputs($connection, "PRIVMSG {$inchannel} :{$user}: No problem!\n");
}
if(strpos(substr(strtolower($a1[3]),1),"!7.1") !== false )
{
fputs($connection,"PRIVMSG {$inchannel} :{$user}: iOS 7.1 tweaklist: http://goo.gl/5oxNkN\n");
}
	}
}

function ge($url)
{
  echo "curling $url...\n";
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_HEADER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_USERAGENT, "PHP55");
   $response = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ));
  $response = preg_split( '/([\r\n][\r\n]){2}/', curl_exec( $ch ),2);

	curl_close( $ch );
return $response[1];
}
function karma($userurl)
{
  $dom = new DOMDocument();
@$dom->loadHTML(ge("http://www.jailbreakqa.com" . $userurl));
  $div = $dom->getElementById('user-reputation');
return $div->textContent;
}
function url($user,$html)
{
$dom = new DOMDocument();
@$dom->loadHTML($html);
$opshit = "♦";
$a = $dom->getElementsByTagName('a');
for ($i =0; $i < $a->length; $i++) {
  $elem = $a->item($i);
  $cont = strtolower($elem->textContent);
  $href = $elem->getAttribute("href");
  if($cont == $user)
  {
    return $href;
  }
	elseif($cont == $user . " ".  $opshit)
	{
		return $href;
	}
	elseif($cont == $user . " ". $opshit . $opshit)
{
	return $href;
}
}
return false;
}
?>
