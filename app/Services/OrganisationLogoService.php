<?php

namespace App\Services;

use App\Models\Organisation\Organisation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Organisations' logos. Uploads are scaled to fit (not cropped: wide logos would lose their ends) and
 * re-encoded as one .webp with any transparency kept, which also drops EXIF data.
 */
class OrganisationLogoService
{
    // px; the largest side, never upscaled. Logos show at up to 64px, on 3x screens and more
    public const MAX_SIZE = 512;

    public const DISK = 'public';

    public function store(Organisation $organisation, UploadedFile $file): void
    {
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($source === false) {
            throw ValidationException::withMessages(['logo' => "That image couldn't be read. Try saving it again as a PNG or JPG."]);
        }

        $logo = $this->fit($source);
        imagedestroy($source);

        // a new name each upload, so cached copies of the old logo are never shown
        $path = 'orgs/' . $organisation->id . '-' . Str::lower(Str::random(10)) . '.webp';

        ob_start();
        imagewebp($logo, null, 80);
        Storage::disk(self::DISK)->put($path, ob_get_clean());
        imagedestroy($logo);

        $this->deleteFile($organisation->logo);
        $organisation->logo = $path;
        $organisation->save();
    }

    public function delete(Organisation $organisation): void
    {
        $this->deleteFile($organisation->logo);
        $organisation->logo = null;
        $organisation->save();
    }

    /**
     * Logos from before WebP are plain uploads under orgs/, deleted the same way.
     */
    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    private function fit(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::MAX_SIZE / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $logo = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($logo, false);
        imagesavealpha($logo, true);
        imagefill($logo, 0, 0, imagecolorallocatealpha($logo, 0, 0, 0, 127));

        imagecopyresampled($logo, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $logo;
    }
}
