<?php
while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/json');

// ── helpers ────────────────────────────────────────────────────────────────

function json_out(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function validate_api_key(): void
{
    $expected = 'bitbet2026';
    $provided = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!hash_equals($expected, $provided)) {
        json_out(['success' => false, 'reason' => 'unauthorized'], 401);
    }
}

// ── request guards ─────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'reason' => 'method_not_allowed'], 405);
}

validate_api_key();

// ── parse body ─────────────────────────────────────────────────────────────

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data) || empty($data['site']) || empty($data['player']) || empty($data['password'])) {
    json_out(['success' => false, 'reason' => 'missing_fields'], 400);
}

$site     = $data['site'];
$username = trim((string) $data['player']);
$password = (string) $data['password'];

// ── route by site ──────────────────────────────────────────────────────────

switch ($site) {

    case 'bitbet':
        $result = call_bitbet($username, $password);
        if (!$result['ok']) {
            json_out(['success' => false, 'reason' => $result['reason']]);
        }
        $valid = parse_bitbet_result($result['body']);
        json_out(['success' => $valid, 'reason' => $valid ? '' : 'invalid_credentials']);
        break;

    // Add new cases here manually as needed:
    // case 'othersite':
    //     ...
    //     break;

    default:
        json_out(['success' => false, 'reason' => 'unknown_site'], 400);
}

// ── Bitbet ─────────────────────────────────────────────────────────────────

function call_bitbet(string $username, string $password): array
{
    $url    = 'https://wager.bitbet.com/cloud/api/System/authenticateCustomer';
    $domain = 'wager.bitbet.com';

    $payload = http_build_query([
        'customerID'    => strtoupper($username),
        'password'      => strtoupper($password),
        'state'         => 'true',
        'multiaccount'  => '1',
        'response_type' => 'code',
        'client_id'     => strtoupper($username),
        'domain'        => $domain,
        'redirect_uri'  => $domain,
        'operation'     => 'authenticateCustomer',
        'token'         => '',
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; ZYTOM-Validator/1.0)',
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Authorization: Bearer ',
        ],
    ]);

    $body     = curl_exec($ch);
    $errno    = curl_errno($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno || $body === false || $httpCode !== 200) {
        return ['ok' => false, 'reason' => 'connection_error'];
    }

    return ['ok' => true, 'body' => $body];
}

function parse_bitbet_result(string $body): bool
{
    $json = json_decode($body, true);
    return is_array($json) && !empty($json['accountInfo']);
}
