<?PHP
require_once __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;
$clientid = getenv('clientid');

// Functions to get public keys
function loadKeysFromAzure($string_microsoftPublicKeyURL) {
    $array_keys = array();

    $jsonString_microsoftPublicKeys = file_get_contents($string_microsoftPublicKeyURL);
    $array_microsoftPublicKeys = json_decode($jsonString_microsoftPublicKeys, true);

    foreach($array_microsoftPublicKeys['keys'] as $array_publicKey) {
        $string_certText = "-----BEGIN CERTIFICATE-----\r\n".chunk_split($array_publicKey['x5c'][0],64)."-----END CERTIFICATE-----\r\n";
        $array_keys[$array_publicKey['kid']] = getPublicKeyFromX5C($string_certText);
    }

    return $array_keys;
}

function getPublicKeyFromX5C($string_certText) {
    $object_cert = openssl_x509_read($string_certText);
    $object_pubkey = openssl_pkey_get_public($object_cert);
    $array_publicKey = openssl_pkey_get_details($object_pubkey);
    return $array_publicKey['key'];
}

// JWT Validation
$headers = getallheaders();
$authorization = explode(' ', $headers['Authorization']);
$accessToken = $authorization[1];

if ($accessToken == "") {
	echo "No access token retrieved.";
} else {
	$string_microsoftPublicKeyURL = 'https://login.windows.net/common/discovery/keys';
	$array_publicKeysWithKIDasArrayKey = loadKeysFromAzure($string_microsoftPublicKeyURL);
	
	$token = JWT::decode($accessToken, $array_publicKeysWithKIDasArrayKey, array('RS256'));
	print_r($token);
	if ($token->aud == $clientid) {
		echo "Validated";
	}
}

?>