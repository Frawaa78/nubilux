<?php
// Email service using SendGrid API
require_once __DIR__ . '/../core/Environment.php';

class EmailService {
    private static function getApiKey() {
        return Environment::get('SENDGRID_API_KEY', 'your_sendgrid_api_key_here');
    }
    
    private static $fromEmail = 'noreply@nubilux.com';
    private static $fromName = 'Nubilux';
    
    public static function sendVerificationEmail($toEmail, $firstName, $verificationToken) {
        $verificationUrl = "https://nubilux.com/verify-email.php?token=" . $verificationToken;
        
        $subject = "Verifiser din e-postadresse - Nubilux";
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>Nubilux</h1>
                <p style='color: white; margin: 10px 0 0 0;'>Smart husstand assistent</p>
            </div>
            <div style='padding: 30px; background: #f8f9fa;'>
                <h2 style='color: #333;'>Hei {$firstName}!</h2>
                <p style='color: #555; line-height: 1.6;'>
                    Takk for at du registrerte deg hos Nubilux! For å fullføre registreringen må du verifisere e-postadressen din.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$verificationUrl}' 
                       style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Verifiser e-postadresse
                    </a>
                </div>
                <p style='color: #777; font-size: 14px;'>
                    Hvis knappen ikke fungerer, kan du kopiere og lime inn denne lenken i nettleseren din:<br>
                    <a href='{$verificationUrl}'>{$verificationUrl}</a>
                </p>
                <p style='color: #777; font-size: 14px;'>
                    Denne lenken utløper om 24 timer.
                </p>
            </div>
            <div style='padding: 20px; background: #e9ecef; text-align: center; color: #6c757d; font-size: 12px;'>
                <p>Hvis du ikke opprettet en konto hos Nubilux, kan du trygt ignorere denne e-posten.</p>
                <p>&copy; 2025 Nubilux. Alle rettigheter forbeholdt.</p>
            </div>
        </div>";
        
        $textContent = "Hei {$firstName}!\n\nTakk for at du registrerte deg hos Nubilux! For å fullføre registreringen må du verifisere e-postadressen din.\n\nKlikk på denne lenken: {$verificationUrl}\n\nDenne lenken utløper om 24 timer.\n\nHvis du ikke opprettet en konto hos Nubilux, kan du trygt ignorere denne e-posten.\n\n© 2025 Nubilux";
        
        return self::sendEmail($toEmail, $subject, $htmlContent, $textContent);
    }
    
    public static function sendPasswordResetEmail($toEmail, $firstName, $resetToken) {
        $resetUrl = "https://nubilux.com/reset-password.php?token=" . $resetToken;
        
        $subject = "Tilbakestill passord - Nubilux";
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>Nubilux</h1>
                <p style='color: white; margin: 10px 0 0 0;'>Smart husstand assistent</p>
            </div>
            <div style='padding: 30px; background: #f8f9fa;'>
                <h2 style='color: #333;'>Hei {$firstName}!</h2>
                <p style='color: #555; line-height: 1.6;'>
                    Du har bedt om å tilbakestille passordet ditt for Nubilux-kontoen din.
                </p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetUrl}' 
                       style='background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Tilbakestill passord
                    </a>
                </div>
                <p style='color: #777; font-size: 14px;'>
                    Hvis knappen ikke fungerer, kan du kopiere og lime inn denne lenken i nettleseren din:<br>
                    <a href='{$resetUrl}'>{$resetUrl}</a>
                </p>
                <p style='color: #777; font-size: 14px;'>
                    Denne lenken utløper om 1 time av sikkerhetshensyn.
                </p>
            </div>
            <div style='padding: 20px; background: #e9ecef; text-align: center; color: #6c757d; font-size: 12px;'>
                <p>Hvis du ikke ba om å tilbakestille passordet, kan du trygt ignorere denne e-posten.</p>
                <p>&copy; 2025 Nubilux. Alle rettigheter forbeholdt.</p>
            </div>
        </div>";
        
        $textContent = "Hei {$firstName}!\n\nDu har bedt om å tilbakestille passordet ditt for Nubilux-kontoen din.\n\nKlikk på denne lenken: {$resetUrl}\n\nDenne lenken utløper om 1 time av sikkerhetshensyn.\n\nHvis du ikke ba om å tilbakestille passordet, kan du trygt ignorere denne e-posten.\n\n© 2025 Nubilux";
        
        return self::sendEmail($toEmail, $subject, $htmlContent, $textContent);
    }
    
    private static function sendEmail($to, $subject, $htmlContent, $textContent) {
        $data = [
            'personalizations' => [
                [
                    'to' => [
                        [
                            'email' => $to
                        ]
                    ]
                ]
            ],
            'from' => [
                'email' => self::$fromEmail,
                'name' => self::$fromName
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/plain',
                    'value' => $textContent
                ],
                [
                    'type' => 'text/html',
                    'value' => $htmlContent
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . self::getApiKey(),
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode >= 200 && $httpCode < 300;
    }
}
?>