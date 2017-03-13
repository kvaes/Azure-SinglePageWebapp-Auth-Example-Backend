<?PHP
require_once __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;

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
function validateAccessToken() {
	$clientid = getenv('clientid');
	$headers = getallheaders();
	$authorization = explode(' ', $headers['Authorization']);
	$accessToken = $authorization[1];

	if ($accessToken == "") {
		echo "No access token retrieved";
	} else {
		$string_microsoftPublicKeyURL = 'https://login.windows.net/common/discovery/keys';
		$array_publicKeysWithKIDasArrayKey = loadKeysFromAzure($string_microsoftPublicKeyURL);	
		$token = JWT::decode($accessToken, $array_publicKeysWithKIDasArrayKey, array('RS256'));
		if ($token->aud == $clientid) {
			return $token;
		}
	}
}

$token = validateAccessToken();
if ($token <> "") {
	echo '
{
	"0001": {
		"ID": "Mittens",
		"Description": "Mittens - Destroyer of pillows!"
	},
	"0002": {
		"ID": "Buff",
		"Description": "Buff - Lover of cat food..."
	},
	"0003": {
		"ID": "Fluffy",
		"Description": "Fluffy - Where no hair has gone before!"
	}
}
	';
} else {
	die();
}

	
?>