<?php

declare(strict_types=1);

class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, ?string $textBody = null): array
    {
        $transport = mb_strtolower(trim((string)(getenv('MAIL_TRANSPORT') ?: 'mail')));
        if ($transport === 'smtp') {
            return self::sendViaSmtp($to, $subject, $htmlBody, $textBody);
        }

        return self::sendViaMailFunction($to, $subject, $htmlBody, $textBody);
    }

    private static function sendViaMailFunction(string $to, string $subject, string $htmlBody, ?string $textBody): array
    {
        $from = trim((string)(getenv('MAIL_FROM') ?: ''));
        if ($from === '') {
            return ['ok' => false, 'error' => 'Configura MAIL_FROM para poder enviar correos.'];
        }

        $fromName = trim((string)(getenv('MAIL_FROM_NAME') ?: 'My Portfolio'));
        $boundary = 'b_' . bin2hex(random_bytes(12));
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $headers = [
            'MIME-Version: 1.0',
            'From: ' . self::formatAddress($from, $fromName),
            'Reply-To: ' . $from,
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        $plain = $textBody !== null && $textBody !== '' ? $textBody : trim(strip_tags($htmlBody));
        $message = '';
        $message .= '--' . $boundary . "\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $plain . "\r\n\r\n";
        $message .= '--' . $boundary . "\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";
        $message .= '--' . $boundary . '--';

        $sent = @mail($to, $encodedSubject, $message, implode("\r\n", $headers));
        if (!$sent) {
            return ['ok' => false, 'error' => 'mail() falló en el servidor. Revisa configuración de correo del hosting.'];
        }

        return ['ok' => true, 'error' => null];
    }

    private static function sendViaSmtp(string $to, string $subject, string $htmlBody, ?string $textBody): array
    {
        $host = trim((string)(getenv('SMTP_HOST') ?: ''));
        $username = trim((string)(getenv('SMTP_USERNAME') ?: ''));
        $password = trim((string)(getenv('SMTP_PASSWORD') ?: ''));
        $port = (int)(getenv('SMTP_PORT') ?: 587);
        $encryption = mb_strtolower(trim((string)(getenv('SMTP_ENCRYPTION') ?: 'tls')));
        $from = trim((string)(getenv('MAIL_FROM') ?: $username));
        $fromName = trim((string)(getenv('MAIL_FROM_NAME') ?: 'My Portfolio'));

        if ($host === '' || $username === '' || $password === '' || $from === '') {
            return ['ok' => false, 'error' => 'Configura SMTP_HOST, SMTP_PORT, SMTP_USERNAME, SMTP_PASSWORD y MAIL_FROM.'];
        }

        $remote = self::smtpRemote($host, $port, $encryption);
        $socket = @stream_socket_client($remote, $errno, $errstr, 15);
        if (!is_resource($socket)) {
            return ['ok' => false, 'error' => 'No se pudo abrir conexión SMTP: ' . $errstr . ' (' . $errno . ').'];
        }

        stream_set_timeout($socket, 15);
        $greeting = self::smtpRead($socket);
        if (!self::smtpExpect($greeting, [220])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'SMTP no respondió saludo válido: ' . trim($greeting)];
        }

        $hostName = self::smtpHeloDomain();
        self::smtpWrite($socket, 'EHLO ' . $hostName);
        $ehlo = self::smtpRead($socket, true);
        if (!self::smtpExpect($ehlo, [250])) {
            self::smtpWrite($socket, 'HELO ' . $hostName);
            $helo = self::smtpRead($socket);
            if (!self::smtpExpect($helo, [250])) {
                fclose($socket);
                return ['ok' => false, 'error' => 'EHLO/HELO rechazado: ' . trim($ehlo . ' ' . $helo)];
            }

            $ehlo = $helo;
        }

        if ($encryption === 'tls') {
            self::smtpWrite($socket, 'STARTTLS');
            $tlsResponse = self::smtpRead($socket);
            if (!self::smtpExpect($tlsResponse, [220])) {
                fclose($socket);
                return ['ok' => false, 'error' => 'STARTTLS rechazado: ' . trim($tlsResponse)];
            }

            $cryptoEnabled = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if ($cryptoEnabled !== true) {
                $cryptoError = (string)((error_get_last()['message'] ?? ''));
                fclose($socket);
                return ['ok' => false, 'error' => 'No se pudo activar cifrado TLS para SMTP.' . ($cryptoError !== '' ? ' ' . $cryptoError : '')];
            }

            self::smtpWrite($socket, 'EHLO ' . $hostName);
            $ehloTls = self::smtpRead($socket, true);
            if (!self::smtpExpect($ehloTls, [250])) {
                fclose($socket);
                return ['ok' => false, 'error' => 'EHLO tras TLS rechazado: ' . trim($ehloTls)];
            }

            $ehlo = $ehloTls;
        }

        $authResult = self::smtpAuthenticate($socket, $ehlo, $username, $password);
        if (!(bool)$authResult['ok']) {
            fclose($socket);
            return ['ok' => false, 'error' => (string)($authResult['error'] ?? 'No se pudo autenticar en SMTP.')];
        }

        $envelopeFrom = $from;
        self::smtpWrite($socket, 'MAIL FROM:<' . $envelopeFrom . '>');
        $mailFrom = self::smtpRead($socket);
        if (!self::smtpExpect($mailFrom, [250])) {
            $canRetryWithUsername = $username !== '' && mb_strtolower($envelopeFrom) !== mb_strtolower($username);
            if ($canRetryWithUsername) {
                $envelopeFrom = $username;
                self::smtpWrite($socket, 'RSET');
                self::smtpRead($socket);
                self::smtpWrite($socket, 'MAIL FROM:<' . $envelopeFrom . '>');
                $mailFromRetry = self::smtpRead($socket);
                if (!self::smtpExpect($mailFromRetry, [250])) {
                    fclose($socket);
                    return ['ok' => false, 'error' => 'MAIL FROM rechazado: ' . trim($mailFromRetry) . ' (original: ' . trim($mailFrom) . ')'];
                }
            } else {
                fclose($socket);
                return ['ok' => false, 'error' => 'MAIL FROM rechazado: ' . trim($mailFrom)];
            }
        }

        self::smtpWrite($socket, 'RCPT TO:<' . $to . '>');
        $rcptTo = self::smtpRead($socket);
        if (!self::smtpExpect($rcptTo, [250, 251])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'RCPT TO rechazado: ' . trim($rcptTo)];
        }

        self::smtpWrite($socket, 'DATA');
        $dataReady = self::smtpRead($socket);
        if (!self::smtpExpect($dataReady, [354])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'SMTP no aceptó DATA: ' . trim($dataReady)];
        }

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $boundary = 'b_' . bin2hex(random_bytes(12));
        $plain = $textBody !== null && $textBody !== '' ? $textBody : trim(strip_tags($htmlBody));
        $payload = [
            'Date: ' . date(DATE_RFC2822),
            'From: ' . self::formatAddress($envelopeFrom, $fromName),
            'Reply-To: ' . $envelopeFrom,
            'To: ' . $to,
            'Subject: ' . $encodedSubject,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            '',
            '--' . $boundary,
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $plain,
            '',
            '--' . $boundary,
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            '',
            $htmlBody,
            '',
            '--' . $boundary . '--',
            '.',
        ];

        self::smtpWrite($socket, implode("\r\n", $payload), false);
        $queued = self::smtpRead($socket);
        if (!self::smtpExpect($queued, [250])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'Servidor SMTP no confirmó cola: ' . trim($queued)];
        }

        self::smtpWrite($socket, 'QUIT');
        self::smtpRead($socket);
        fclose($socket);

        return ['ok' => true, 'error' => null];
    }

    private static function smtpWrite($socket, string $line, bool $addCrLf = true): void
    {
        fwrite($socket, $line . ($addCrLf ? "\r\n" : ''));
    }

    private static function smtpRead($socket, bool $multiLine = false): string
    {
        $response = '';
        while (!feof($socket)) {
            $line = fgets($socket, 512);
            if ($line === false) {
                break;
            }

            $response .= $line;

            if (!$multiLine) {
                break;
            }

            if (preg_match('/^\d{3}\s/', $line) === 1) {
                break;
            }
        }

        return $response;
    }

    private static function smtpExpect(string $response, array $codes): bool
    {
        if (preg_match('/^(\d{3})/m', $response, $matches) !== 1) {
            return false;
        }

        return in_array((int)$matches[1], $codes, true);
    }

    private static function smtpAuthenticate($socket, string $ehloResponse, string $username, string $password): array
    {
        $preferredMode = mb_strtolower(trim((string)(getenv('SMTP_AUTH_MODE') ?: 'auto')));
        $serverMethods = self::smtpAuthMethods($ehloResponse);

        $methodsToTry = self::smtpAuthMethodsToTry($preferredMode, $serverMethods);
        $errors = [];

        foreach ($methodsToTry as $method) {
            $result = $method === 'plain'
                ? self::smtpAuthPlain($socket, $username, $password)
                : self::smtpAuthLogin($socket, $username, $password);

            if ((bool)($result['ok'] ?? false)) {
                return ['ok' => true, 'error' => null];
            }

            $errors[] = strtoupper($method) . ': ' . (string)($result['error'] ?? 'Error desconocido');
        }

        return ['ok' => false, 'error' => 'Autenticación SMTP falló. Intentos: ' . implode(' | ', $errors)];
    }

    private static function smtpAuthMethodsToTry(string $preferredMode, array $serverMethods): array
    {
        if ($preferredMode === 'login' || $preferredMode === 'plain') {
            return [$preferredMode];
        }

        $supported = array_values(array_intersect(['plain', 'login'], $serverMethods));
        if ($supported === []) {
            return ['login', 'plain'];
        }

        if (in_array('plain', $supported, true) && in_array('login', $supported, true)) {
            return ['plain', 'login'];
        }

        if (in_array('plain', $supported, true)) {
            return ['plain', 'login'];
        }

        return ['login', 'plain'];
    }

    private static function smtpAuthPlain($socket, string $username, string $password): array
    {
        $payload = base64_encode("\0" . $username . "\0" . $password);
        self::smtpWrite($socket, 'AUTH PLAIN ' . $payload);
        $response = self::smtpRead($socket);

        if (self::smtpExpect($response, [235])) {
            return ['ok' => true, 'error' => null];
        }

        return ['ok' => false, 'error' => 'AUTH PLAIN rechazado: ' . trim($response)];
    }

    private static function smtpAuthLogin($socket, string $username, string $password): array
    {
        self::smtpWrite($socket, 'AUTH LOGIN');
        $authPromptUser = self::smtpRead($socket);
        if (!self::smtpExpect($authPromptUser, [334])) {
            return ['ok' => false, 'error' => 'AUTH LOGIN rechazado: ' . trim($authPromptUser)];
        }

        self::smtpWrite($socket, base64_encode($username));
        $authPromptPass = self::smtpRead($socket);
        if (!self::smtpExpect($authPromptPass, [334])) {
            return ['ok' => false, 'error' => 'Usuario SMTP rechazado: ' . trim($authPromptPass)];
        }

        self::smtpWrite($socket, base64_encode($password));
        $authResult = self::smtpRead($socket);
        if (!self::smtpExpect($authResult, [235])) {
            return ['ok' => false, 'error' => 'Credenciales SMTP inválidas: ' . trim($authResult)];
        }

        return ['ok' => true, 'error' => null];
    }

    private static function smtpAuthMethods(string $ehloResponse): array
    {
        $methods = [];

        foreach (preg_split('/\\r?\\n/', $ehloResponse) ?: [] as $line) {
            if (preg_match('/AUTH(?:=|\\s+)([^\\r\\n]+)/i', $line, $matches) !== 1) {
                continue;
            }

            $lineMethods = preg_split('/\\s+/', trim((string)$matches[1])) ?: [];
            foreach ($lineMethods as $method) {
                $normalized = mb_strtolower(trim($method));
                if ($normalized !== '') {
                    $methods[] = $normalized;
                }
            }
        }

        return array_values(array_unique($methods));
    }

    private static function smtpRemote(string $host, int $port, string $encryption): string
    {
        if ($encryption === 'ssl') {
            return 'ssl://' . $host . ':' . $port;
        }

        return 'tcp://' . $host . ':' . $port;
    }

    private static function smtpHeloDomain(): string
    {
        $configured = trim((string)(getenv('SMTP_HELO_DOMAIN') ?: ''));
        if ($configured !== '') {
            return $configured;
        }

        $mailFrom = trim((string)(getenv('MAIL_FROM') ?: ''));
        if ($mailFrom !== '' && strpos($mailFrom, '@') !== false) {
            $domain = trim((string)substr($mailFrom, (int)strpos($mailFrom, '@') + 1));
            if ($domain !== '') {
                return $domain;
            }
        }

        $appUrl = trim((string)(getenv('APP_URL') ?: ''));
        $host = (string)parse_url($appUrl, PHP_URL_HOST);
        if ($host !== '') {
            return $host;
        }

        return 'localhost';
    }

    private static function formatAddress(string $email, string $name): string
    {
        $safeName = str_replace(['"', "\r", "\n"], '', $name);
        if ($safeName === '') {
            return $email;
        }

        return '"' . addslashes($safeName) . '" <' . $email . '>';
    }
}
