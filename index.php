<?PHP

require_once __DIR__ . '/vendor/autoload.php';

$provider = new TheNetworg\OAuth2\Client\Provider\Azure([
    'clientId'          => getenv('clientId'),
    'clientSecret'      => getenv('clientSecret'),
    'redirectUri'       => getenv('redirectUri')
]);

$headers = getallheaders();

print_r($headers);

$authorization = explode(' ', $headers['Authorization']);
$accessToken = $authorization[1];

try {
    $claims = $provider->validateAccessToken($accessToken);
} catch (Exception $e) {
    echo "Something went wrong :-(";
}

$graphAccessToken = $provider->getAccessToken('jwt_bearer', [
    'resource' => 'https://graph.microsoft.com/v1.0/',
    'assertion' => $accessToken,
    'requested_token_use' => 'on_behalf_of'
]);

$me = $provider->get('https://graph.microsoft.com/v1.0/me', $graphAccessToken);
print_r($me);

?>