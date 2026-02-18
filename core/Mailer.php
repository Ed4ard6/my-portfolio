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

        $remote = ($encryption === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
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

        $hostName = trim((string)(getenv('SMTP_HELO_DOMAIN') ?: 'localhost'));
        self::smtpWrite($socket, 'EHLO ' . $hostName);
        $ehlo = self::smtpRead($socket, true);
        if (!self::smtpExpect($ehlo, [250])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'EHLO rechazado: ' . trim($ehlo)];
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
                fclose($socket);
                return ['ok' => false, 'error' => 'No se pudo activar cifrado TLS para SMTP.'];
            }

            self::smtpWrite($socket, 'EHLO ' . $hostName);
            $ehloTls = self::smtpRead($socket, true);
            if (!self::smtpExpect($ehloTls, [250])) {
                fclose($socket);
                return ['ok' => false, 'error' => 'EHLO tras TLS rechazado: ' . trim($ehloTls)];
            }
        }

        self::smtpWrite($socket, 'AUTH LOGIN');
        $authPromptUser = self::smtpRead($socket);
        if (!self::smtpExpect($authPromptUser, [334])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'AUTH LOGIN rechazado: ' . trim($authPromptUser)];
        }

        self::smtpWrite($socket, base64_encode($username));
        $authPromptPass = self::smtpRead($socket);
        if (!self::smtpExpect($authPromptPass, [334])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'Usuario SMTP rechazado: ' . trim($authPromptPass)];
        }

        self::smtpWrite($socket, base64_encode($password));
        $authResult = self::smtpRead($socket);
        if (!self::smtpExpect($authResult, [235])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'Credenciales SMTP inválidas: ' . trim($authResult)];
        }

        self::smtpWrite($socket, 'MAIL FROM:<' . $from . '>');
        $mailFrom = self::smtpRead($socket);
        if (!self::smtpExpect($mailFrom, [250])) {
            fclose($socket);
            return ['ok' => false, 'error' => 'MAIL FROM rechazado: ' . trim($mailFrom)];
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
            'From: ' . self::formatAddress($from, $fromName),
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

    private static function formatAddress(string $email, string $name): string
    {
        $safeName = str_replace(['"', "\r", "\n"], '', $name);
        if ($safeName === '') {
            return $email;
        }

        return '"' . addslashes($safeName) . '" <' . $email . '>';
    }
}

