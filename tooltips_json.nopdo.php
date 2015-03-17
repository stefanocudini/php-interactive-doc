<?

function getmicrotime()
{
   list($usec, $sec) = explode(" ",microtime());
   return ((float)$usec + (float)$sec);
}
# avvia il timer per calcolare il tempo di generazione della pagina
$tstart = getmicrotime();


$local = mysql_pconnect("127.0.0.1","labs","l4b5");
mysql_select_db("labs_phpdoc",$local);

$func = isset($_GET['func']) ? $_GET['func'] : 'abs';
$ofunc = str_replace('*','',$_GET['func']);

###$func = $func{0}=='^' ? $func.'*' : $func;

if($func{0}!='*' and $func{strlen($func)-1}!='*')
  $func .= '*';

switch($_GET['mode'])
{
  case 'name':
  $mode = 'name';
  break;
  case 'des':
  $mode = 'des';
  $func = $func{0}!='*' ? '*'.$func : $func;
  break;
}

$func = str_replace('*','%',$func);
$func = str_replace('_','\_',$func);
$sql = "SELECT * FROM functions WHERE $mode LIKE '$func' ORDER BY name";

$res = mysql_query($sql);

header("Content-type: text/plain");
echo '{"key":"'.$func.'","dati":[';
//array di array JSON
while($rig = mysql_fetch_array($res))
{
  echo "[\"${rig['name']}\",\"${rig['des']}\",\"${rig['proto']}\",\"${rig['params']}\",\"${rig['return']}\"],\n";
}
#  echo "[\"".str_replace($ofunc,'<u>'.$ofunc.'</u>',$rig['name'])."\",\"${rig['des']}\",\"${rig['extension']}\"],";

$time = round((getmicrotime()-$tstart)*1000,3);

echo '],"time":"'.$time.'"}';

mysql_close($local);



?>
