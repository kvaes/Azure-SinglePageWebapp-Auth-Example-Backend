<?PHP
require_once __DIR__ . '/vendor/autoload.php';

use Emarref\Jwt\Claim;
$jwt = new Emarref\Jwt\Jwt();

$headers = getallheaders();
$authorization = explode(' ', $headers['Authorization']);
$accessToken = $authorization[1];

if ($accessToken == "") {
	echo "No access token retrieved.";
} else {
	$token = $jwt->deserialize($accessToken);
	print_r($token);
}

?>