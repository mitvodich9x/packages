<?php

namespace Vgplay\Games\Traits;

use Illuminate\Support\Facades\Storage;

trait HasFtpAssets
{
    protected function getFtpUrlIfExists(?string $relativePath): ?string
    {
        if ($relativePath && Storage::disk('ftp')->exists($relativePath)) {
            return config('filesystems.disks.ftp.url') . '/' . $relativePath;
        }

        return null;
    }

    protected function stripFtpPrefix(?string $url): ?string
    {
        return str_replace(config('filesystems.disks.ftp.url') . '/', '', $url);
    }

    public function getAssetUrl(string $key): ?string
    {
        $path = $this->assets[$key] ?? null;
        return $this->getFtpUrlIfExists($this->stripFtpPrefix($path));
    }

    public function getAdminImageUrl(string $key): ?string
    {
        $path = $this->admin[$key] ?? null;
        return $this->getFtpUrlIfExists($this->stripFtpPrefix($path));
    }
}
