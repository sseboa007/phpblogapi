<?php

    class JwtGenerator
    {
        private $key;

        public function __construct(string $key)
        {
            $this->key = $key;
        }

        public function generateToken(array $payload, int $exp = 3600): string
        {
            $header = $this->base64_url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
            $issuedAt = time();
            $expiresAt = $issuedAt + $exp;

            $payload["iat"] = $issuedAt;
            $payload["exp"] = $expiresAt;

            $payload = $this->base64_url_encode(json_encode($payload));

            $signature = hash_hmac('sha256', "$header.$payload", $this->key, true);
            $signature = $this->base64_url_encode($signature);

            return "$header.$payload.$signature";
        }

        public function validateToken(string $token): bool
        {
            $token_parts = explode('.', $token);
            if (count($token_parts) !== 3) {
                return false;
            }

            $header = $this->base64_url_decode($token_parts[0]);
            $payload = $this->base64_url_decode($token_parts[1]);
            $signature = $this->base64_url_decode($token_parts[2]);

            $header_data = json_decode($header);
            if (!isset($header_data->alg) || $header_data->alg !== 'HS256') {
                return false;
            }

            $encodedHeader = $this->base64_url_encode($header);
            $encodedPayload = $this->base64_url_encode($payload);

            $valid_signature = hash_hmac('sha256', "$encodedHeader.$encodedPayload", $this->key, true);
            $valid_signature = $this->base64_url_encode($valid_signature);

            return ($token_parts[2] === $valid_signature);
        }

        public function decodeToken(string $token): object
        {
            $payload = $this->base64_url_decode(explode('.', $token)[1]);
            return json_decode($payload);
        }

        /**
         * per https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid/15875555#15875555
         */
        private function base64_url_encode($text): string {
            return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
        }

        private function base64_url_decode($text): string {
            return str_replace(['-', '_', ''], ['+', '/', '='], base64_decode($text));
        }
    }

?>