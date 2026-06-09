<?php
// EARNNOVA - PlatoBoost PHP Integration
// Ported from the Python reference implementation

require_once __DIR__ . '/../includes/config.php';

class Platoboost {
    private $session;
    private $hostname;
    private $identifier;
    private $cachedLink = null;
    private $cachedTime = null;
    private $service;
    private $secret;
    private $useNonce;
    private $callback;

    public function __construct($callback = null) {
        $this->service = PLATOBOOST_SERVICE_ID;
        $this->secret = PLATOBOOST_SECRET;
        $this->useNonce = true;
        $this->callback = $callback;

        $this->session = curl_init();
        $this->hostname = 'https://api.platoboost.com';

        // Test connectivity
        $response = $this->makeRequest('GET', $this->hostname . '/public/connectivity');
        if (!$response || !isset($response['success']) || $response['success'] !== true) {
            $this->hostname = 'https://api.platoboost.net';
        }

        $this->identifier = $this->generateIdentifier();
        $this->cacheLink();
    }

    private function generateIdentifier() {
        $components = [
            php_uname('n'),
            php_uname('r'),
            php_uname('m'),
            $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'
        ];
        return hash('sha256', implode('|', $components));
    }

    private function makeRequest($method, $url, $data = null) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'User-Agent: Platoboost PHP Client/1.0',
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return null;
        }

        return [
            'http_code' => $httpCode,
            'body' => json_decode($response, true)
        ];
    }

    private function generateNonce() {
        if ($this->useNonce) {
            return md5(microtime(true));
        }
        return 'empty';
    }

    private function cacheLink() {
        $now = time();
        if ($this->cachedLink === null || $this->cachedTime === null || ($now - $this->cachedTime) > 300) {
            $response = $this->makeRequest('POST', $this->hostname . '/public/start', [
                'service' => $this->service,
                'identifier' => $this->identifier
            ]);

            if ($response === null) {
                if ($this->callback) call_user_func($this->callback, 'Failed to connect to server');
                return false;
            }

            $decoded = $response['body'];
            $httpCode = $response['http_code'];

            if ($httpCode === 200 && isset($decoded['success']) && $decoded['success'] === true) {
                $this->cachedLink = $decoded['data']['url'];
                $this->cachedTime = $now;
                return $this->cachedLink;
            } elseif ($httpCode === 429) {
                if ($this->callback) call_user_func($this->callback, 'Rate limited, please wait 20 seconds');
                return false;
            } else {
                $msg = $decoded['message'] ?? 'Failed to cache link';
                if ($this->callback) call_user_func($this->callback, $msg);
                return false;
            }
        }
        return $this->cachedLink;
    }

    public function getLink() {
        $link = $this->cacheLink();
        if ($link) {
            return $this->cachedLink;
        }
        return null;
    }

    private function redeemKey($key) {
        $nonce = $this->generateNonce();
        $endpoint = $this->hostname . '/public/redeem/' . $this->service;
        
        $body = [
            'identifier' => $this->identifier,
            'key' => $key
        ];

        if ($this->useNonce) {
            $body['nonce'] = $nonce;
        }

        $response = $this->makeRequest('POST', $endpoint, $body);
        
        if ($response === null) {
            if ($this->callback) call_user_func($this->callback, 'Server returned invalid status');
            return false;
        }

        $decoded = $response['body'];
        $httpCode = $response['http_code'];

        if ($httpCode === 200) {
            if ($decoded['success'] === true) {
                $valid = $decoded['data']['valid'];
                if ($valid) {
                    if ($this->useNonce) {
                        $expectedHash = hash('sha256', strtolower(strval($valid)) . '-' . $nonce . '-' . $this->secret);
                        if ($decoded['data']['hash'] === $expectedHash) {
                            return $valid;
                        } else {
                            if ($this->callback) call_user_func($this->callback, 'Failed to verify integrity');
                            return false;
                        }
                    }
                    return true;
                } else {
                    if ($this->callback) call_user_func($this->callback, 'Key is invalid');
                    return false;
                }
            } else {
                $msg = $decoded['message'] ?? 'Unknown error';
                if (strpos($msg, 'unique constraint violation') !== false) {
                    if ($this->callback) call_user_func($this->callback, 'You already have an active key');
                } else {
                    if ($this->callback) call_user_func($this->callback, $msg);
                }
                return false;
            }
        } elseif ($httpCode === 429) {
            if ($this->callback) call_user_func($this->callback, 'Rate limited, please wait 20 seconds');
            return false;
        } else {
            if ($this->callback) call_user_func($this->callback, 'Server returned invalid status code');
            return false;
        }
    }

    public function verifyKey($key) {
        $nonce = $this->generateNonce();
        $endpoint = $this->hostname . '/public/whitelist/' . $this->service . '?identifier=' . urlencode($this->identifier) . '&key=' . urlencode($key);

        if ($this->useNonce) {
            $endpoint .= '&nonce=' . $nonce;
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response === null) {
            if ($this->callback) call_user_func($this->callback, 'Server returned invalid status');
            return false;
        }

        $decoded = $response['body'];
        $httpCode = $response['http_code'];

        if ($httpCode === 200) {
            if ($decoded['success'] === true) {
                $valid = $decoded['data']['valid'];
                if ($valid) {
                    if ($this->useNonce) {
                        $expectedHash = hash('sha256', strtolower(strval($valid)) . '-' . $nonce . '-' . $this->secret);
                        if ($decoded['data']['hash'] === $expectedHash) {
                            return $valid;
                        } else {
                            if ($this->callback) call_user_func($this->callback, 'Failed to verify integrity');
                            return false;
                        }
                    }
                    return true;
                } else {
                    if (strpos($key, 'KEY_') === 0) {
                        return $this->redeemKey($key);
                    } else {
                        if ($this->callback) call_user_func($this->callback, 'Key is invalid');
                        return false;
                    }
                }
            } else {
                if ($this->callback) call_user_func($this->callback, $decoded['message'] ?? 'Verification failed');
                return false;
            }
        } elseif ($httpCode === 429) {
            if ($this->callback) call_user_func($this->callback, 'Rate limited, please wait 20 seconds');
            return false;
        } else {
            if ($this->callback) call_user_func($this->callback, 'Server returned invalid status code');
            return false;
        }
    }

    public function getFlag($name) {
        $nonce = $this->generateNonce();
        $endpoint = $this->hostname . '/public/flag/' . $this->service . '?name=' . urlencode($name);

        if ($this->useNonce) {
            $endpoint .= '&nonce=' . $nonce;
        }

        $response = $this->makeRequest('GET', $endpoint);

        if ($response === null) {
            return null;
        }

        $decoded = $response['body'];
        $httpCode = $response['http_code'];

        if ($httpCode === 200 && $decoded['success'] === true) {
            $value = $decoded['data']['value'];
            $strValue = is_bool($value) ? strtolower(strval($value)) : strval($value);

            if ($this->useNonce) {
                $expectedHash = hash('sha256', $strValue . '-' . $nonce . '-' . $this->secret);
                if ($decoded['data']['hash'] === $expectedHash) {
                    return $value;
                } else {
                    if ($this->callback) call_user_func($this->callback, 'Failed to verify integrity');
                    return null;
                }
            }
            return $value;
        }

        return null;
    }

    public function __destruct() {
        if ($this->session) {
            curl_close($this->session);
        }
    }
}
