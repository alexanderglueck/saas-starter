<?php

namespace App\Traits;

use DateTime;
use Illuminate\Support\Carbon;
use ParagonIE\ConstantTime\Base32;

trait HandlesCodes
{
    protected $window = 1;
    protected $seconds = 30;
    protected $digits = 6;
    protected $algo = "sha1";

    /**
     * Current instance of the Cache Repository.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * String to prefix the Cache key.
     *
     * @var string
     */
    protected $prefix = "2fa.code";

    /**
     * Initializes the current trait.
     *
     * @throws \Exception
     */
    protected function initializeHandlesCodes()
    {
        $this->cache = cache()->store();
    }

    /**
     * Validates a given code, optionally for a given timestamp and future window.
     *
     * @param string $code
     * @param int|string|\Illuminate\Support\Carbon|\Datetime $at
     * @param int $window
     * @return bool
     */
    public function validateCode(string $code, $at = 'now', int $window = null): bool
    {
        if ($this->codeHasBeenUsed($code)) {
            return false;
        }

        $window = $window ?? $this->window;

        for ($i = 0; $i <= $window; ++$i) {
            if (hash_equals($this->makeTwoFactorCode($at, -$i), $code)) {
                $this->setCodeHasUsed($code, $at);
                return true;
            }
        }

        return false;
    }

    /**
     * Creates a Code for a given timestamp, optionally by a given period offset.
     *
     * @param int|string|\Illuminate\Support\Carbon|\Datetime $at
     * @param int $offset
     * @return string
     */
    public function makeTwoFactorCode($at = 'now', int $offset = 0): string
    {
        return $this->generateCode(
            $this->getTimestampFromPeriod($at, $offset)
        );
    }

    /**
     * Generates a valid Code for a given timestamp.
     *
     * @param int $timestamp
     * @return string
     */
    protected function generateCode(int $timestamp)
    {
        $hmac = hash_hmac(
            $this->algo,
            $this->timestampToBinary($this->getPeriodsFromTimestamp($timestamp)),
            $this->getBinarySecret(),
            true
        );

        $offset = ord($hmac[strlen($hmac) - 1]) & 0xF;

        $number = (
                ((ord($hmac[$offset + 0]) & 0x7F) << 24) |
                ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
                ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
                (ord($hmac[$offset + 3]) & 0xFF)
            ) % (10 ** $this->digits);

        return str_pad((string)$number, $this->digits, '0', STR_PAD_LEFT);
    }

    /**
     * Creates a 64-bit raw binary string from a timestamp.
     *
     * @param int $timestamp
     * @return string
     */
    protected function timestampToBinary(int $timestamp)
    {
        return pack('N*', 0) . pack('N*', $timestamp);
    }

    /**
     * turn base32 string into rubbish
     * @return string
     */
    protected function getBinarySecret()
    {
        return Base32::decodeUpper($this->tfa_shared_secret);
    }

    /**
     * Return the periods elapsed from the given Timestamp and seconds.
     *
     * @param int $timestamp
     * @return int
     */
    protected function getPeriodsFromTimestamp(int $timestamp)
    {
        return (int)(floor($timestamp / $this->seconds));
    }

    /**
     * Get the timestamp from a given elapsed "periods" of seconds.
     *
     * @param int|string|\Datetime|\Illuminate\Support\Carbon $at
     * @param int $period
     * @return int
     */
    protected function getTimestampFromPeriod($at, int $period = 0)
    {
        $periods = ($this->parseTimestamp($at) / $this->seconds) + $period;

        return (int)$periods * $this->seconds;
    }

    /**
     * Normalizes the Timestamp from a string, integer or object.
     *
     * @param int|string|\Datetime|\Illuminate\Support\Carbon $at
     * @return int
     */
    protected function parseTimestamp($at): int
    {
        if ($at instanceof DateTime) {
            return $at->getTimestamp();
        }

        if (is_string($at)) {
            return Carbon::parse($at)->getTimestamp();
        }

        return $at;
    }

    /**
     * Returns the cache key string to save the codes into the cache.
     *
     * @param string $code
     * @return string
     */
    protected function cacheKey(string $code)
    {
        return "{$this->prefix}|{$this->getKey()}|$code";
    }

    /**
     * Checks if the code has been used.
     *
     * @param string $code
     * @return bool
     */
    protected function codeHasBeenUsed(string $code): bool
    {
        return $this->cache->has($this->cacheKey($code));
    }

    /**
     * Sets the Code has used so it can't be used again.
     *
     * @param string $code
     * @param int|string|\Datetime|\Illuminate\Support\Carbon $at
     * @return bool
     */
    protected function setCodeHasUsed(string $code, $at)
    {
        // We will safely set the cache key for the whole lifetime plus window just to be safe.
        return $this->cache->set($this->cacheKey($code), true,
            Carbon::createFromTimestamp($this->getTimestampFromPeriod($at, $this->window + 1))
        );
    }
}
