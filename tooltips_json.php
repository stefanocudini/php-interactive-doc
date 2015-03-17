<?

header("Content-type: text/plain");

function getmicrotime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
# avvia il timer per calcolare il tempo di generazione della pagina
$tstart = getmicrotime();

switch($_GET['dbtype'])
{
	case 'mysql':
		$dsn = "mysql:host=localhost;dbname=labs_phpdoc";
		try {
			$db = new PDO($dsn,'labs','l4b5');
		}catch (PDOException $err) {
			//die("\n".$err->getMessage() );
			die('{"error":"Mysql Assente"}');
		}
	break;
	case 'sqlite':
		$dsn = "sqlite:".getcwd()."/phpdoc.sqlite3";
		$db = new PDO($dsn) or die('{"error":"Sqlite Assente"}');
	break;
	default:
		die('specifica tipo db');
}

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
if($_GET['dbtype']=='mysql')
	$func = str_replace('_','\_',$func);
$sql = "SELECT name,des,proto,params,return FROM functions WHERE $mode LIKE '$func' ORDER BY name";

try 
{
	$res = $db->query($sql);
}
catch (PDOException $err) {
	die("\n".$err->getMessage() );
}

$J = array();
$J['key']= $func;

while($rig = $res->fetch(PDO::FETCH_NUM))
	$J['dati'][]= array_values($rig);

#  echo "[\"".str_replace($ofunc,'<u>'.$ofunc.'</u>',$rig['name'])."\",\"${rig['des']}\",\"${rig['extension']}\"],";

$J['time']= round((getmicrotime()-$tstart)*1000,3);

echo json_encode($J);



?>
