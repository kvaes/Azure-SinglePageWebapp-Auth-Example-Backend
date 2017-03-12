<?PHP

require_once __DIR__ . '/vendor/autoload.php';

$headers = getallheaders();
$authorization = explode(' ', $headers['Authorization']);
$accessToken = $authorization[1];

use Emarref\Jwt\Claim;
$jwt = new Emarref\Jwt\Jwt();
$token = $jwt->deserialize($accessToken);
print_r($token);

?>