<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function show()
    {
        return view('certificates.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|max:20']);

        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $request->code)
            ->first();

        return view('certificates.verify', compact('certificate'));
    }

    public function showByCode(string $code)
    {
        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $code)
            ->firstOrFail();

        return view('certificates.show', compact('certificate'));
    }

    public function ogImage(string $code)
    {
        $certificate = Certificate::withoutGlobalScopes()
            ->with(['user', 'training', 'company'])
            ->where('certificate_code', $code)
            ->firstOrFail();

        // Check cache
        $cachePath = storage_path("app/og-images/{$code}.png");
        if (file_exists($cachePath) && filemtime($cachePath) > now()->subDay()->timestamp) {
            return response()->file($cachePath, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400']);
        }

        $company = $certificate->company;
        $primary = $this->hexToRgb($company->primary_color ?? '#4f46e5');
        $userName = $certificate->user->name;
        $trainingTitle = $certificate->training->title;
        $companyName = $company->name;
        $titleText = $company->cert_title_text ?? 'CERTIFICADO';
        $date = $certificate->generated_at->format('d/m/Y');

        $w = 1200;
        $h = 630;
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);

        // Colors
        $white = imagecolorallocate($img, 255, 255, 255);
        $primaryC = imagecolorallocate($img, $primary[0], $primary[1], $primary[2]);
        $primaryLight = imagecolorallocate($img, $primary[0], $primary[1], $primary[2]);
        $dark = imagecolorallocate($img, 31, 41, 55);
        $gray = imagecolorallocate($img, 156, 163, 175);
        $lightBg = imagecolorallocate($img, 248, 250, 252);

        // Background
        imagefilledrectangle($img, 0, 0, $w, $h, $white);

        // Left accent bar
        imagefilledrectangle($img, 0, 0, 39, $h, $primaryC);

        // Company name
        $this->drawText($img, 16, $companyName, 80, 90, $primaryC);

        // Title
        $this->drawText($img, 48, $titleText, 80, 195, $primaryC, true);

        // Divider
        imagefilledrectangle($img, 80, 220, 160, 224, $primaryC);

        // Certificamos que
        $this->drawText($img, 13, 'CERTIFICAMOS QUE', 80, 270, $gray);

        // Name
        $this->drawText($img, 36, $userName, 80, 330, $dark, true);

        // Name underline
        imagefilledrectangle($img, 80, 348, 360, 351, $primaryC);

        // Training box bg
        imagefilledrectangle($img, 80, 385, 1120, 475, $lightBg);
        imagefilledrectangle($img, 80, 385, 85, 475, $primaryC);

        // Training text
        $this->drawText($img, 12, 'CONCLUIU COM SUCESSO O TREINAMENTO', 104, 415, $gray);
        $this->drawText($img, 22, $trainingTitle, 104, 452, $dark, true);

        // Footer
        $this->drawText($img, 12, "Emitido em {$date}  |  {$certificate->certificate_code}", 80, 540, $gray);
        $this->drawText($img, 12, 'Verificado por TreinaEdu', 80, 565, $gray);

        // Save
        if (!is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0755, true);
        }
        imagepng($img, $cachePath);
        imagedestroy($img);

        return response()->file($cachePath, ['Content-Type' => 'image/png', 'Cache-Control' => 'public, max-age=86400']);
    }

    private function drawText($img, int $size, string $text, int $x, int $y, $color, bool $bold = false): void
    {
        // Try system fonts, fallback to GD built-in
        $fontPaths = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];

        $font = null;
        foreach ($fontPaths as $fp) {
            if (file_exists($fp)) {
                $font = $fp;
                if ($bold && str_contains($fp, 'Bold')) break;
                if (!$bold && !str_contains($fp, 'Bold')) break;
            }
        }

        if ($font) {
            imagettftext($img, $size, 0, $x, $y, $color, $font, $text);
        } else {
            // Fallback: GD built-in font
            $gdFont = $size > 20 ? 5 : ($size > 14 ? 4 : 3);
            imagestring($img, $gdFont, $x, $y - 10, $text, $color);
        }
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
