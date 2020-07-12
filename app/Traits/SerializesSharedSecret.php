<?php

namespace App\Traits;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

trait SerializesSharedSecret
{
    /**
     * Returns the Shared Secret as an URI.
     *
     * @return string
     */
    public function getTwoFactorAuthUri(): string
    {
        $query = http_build_query([
            'secret' => $this->tfa_shared_secret,
            'issuer' => $issuer = rawurlencode(config('app.name')),
        ], null, '&', PHP_QUERY_RFC3986);

        return "otpauth://totp/$issuer:{$this->email}?$query";
    }

    /**
     * Returns the Shared Secret as a QR Code in SVG format.
     *
     * @return string
     */
    public function getTwoFactorAuthQRCode(): string
    {
        return (new Writer((new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd()))))
            ->writeString($this->getTwoFactorAuthUri());
    }
}
