<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactor {

    private $conn;
    private Google2FA $google2fa;

    public function __construct($conn) {
        $this->conn      = $conn;
        $this->google2fa = new Google2FA();
    }

    /** Generate a new secret key */
    public function generateSecret(): string {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Get the QR code as an inline SVG string.
     * Uses BaconQrCode SVG backend — no Imagick required.
     */
    public function getQRCodeSvg(string $email, string $secret): string {
        $otpAuthUrl = $this->google2fa->getQRCodeUrl('SeedCycle', $email, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($otpAuthUrl);
    }

    /** Verify a TOTP code against a secret */
    public function verify(string $secret, string $code): bool {
        return $this->google2fa->verifyKey($secret, $code);
    }

    /** Save the secret to the user's record */
    public function saveSecret(int $userId, string $secret): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE users SET totp_secret = ? WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'si', $secret, $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Enable 2FA */
    public function enable(int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE users SET totp_enabled = 1 WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Disable 2FA */
    public function disable(int $userId): bool {
        $stmt = mysqli_prepare($this->conn,
            'UPDATE users SET totp_enabled = 0, totp_secret = NULL WHERE id = ?'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    /** Get 2FA status */
    public function getStatus(int $userId): array {
        $stmt = mysqli_prepare($this->conn,
            'SELECT totp_secret, totp_enabled FROM users WHERE id = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $row ?: ['totp_secret' => null, 'totp_enabled' => 0];
    }
}
