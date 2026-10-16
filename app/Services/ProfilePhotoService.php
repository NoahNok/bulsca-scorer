<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Officials' profile photos, shown in place of their initials in Digital Judge.
 * Uploads are cropped square and re-encoded small (which also drops EXIF and GPS
 * data), as a .webp plus a .jpg for browsers without WebP.
 */
class ProfilePhotoService
{
    // px; sharp at the officials sheet's 36px avatars on 3x screens
    public const SIZE = 192;

    public const DISK = 'public';

    public function store(User $user, UploadedFile $file): void
    {
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($source === false) {
            throw new RuntimeException('Unreadable image');
        }

        $source = $this->orient($source, $file);
        $square = $this->cropSquare($source);
        imagedestroy($source);

        // a new name each upload, so cached copies of the old photo are never shown
        $stem = 'avatars/' . $user->id . '-' . Str::lower(Str::random(10));

        $disk = Storage::disk(self::DISK);
        $disk->put("$stem.webp", $this->encode(fn () => imagewebp($square, null, 75)));
        $disk->put("$stem.jpg", $this->encode(fn () => imagejpeg($square, null, 80)));
        imagedestroy($square);

        $this->deleteFiles($user->profile_photo);
        $user->forceFill(['profile_photo' => $stem])->save();
    }

    public function delete(User $user): void
    {
        $this->deleteFiles($user->profile_photo);
        $user->forceFill(['profile_photo' => null])->save();
    }

    public function deleteFiles(?string $stem): void
    {
        if ($stem) {
            Storage::disk(self::DISK)->delete(["$stem.webp", "$stem.jpg"]);
        }
    }

    /**
     * Phones save portrait photos sideways with an EXIF orientation flag, which GD ignores.
     */
    private function orient(\GdImage $image, UploadedFile $file): \GdImage
    {
        if (!function_exists('exif_read_data') || $file->getMimeType() !== 'image/jpeg') {
            return $image;
        }

        $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;

        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => null,
        };

        if ($rotated) {
            imagedestroy($image);
            return $rotated;
        }

        return $image;
    }

    private function cropSquare(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);

        $square = imagecreatetruecolor(self::SIZE, self::SIZE);
        // transparent PNGs go onto white rather than black in the JPEG
        imagefill($square, 0, 0, imagecolorallocate($square, 255, 255, 255));

        imagecopyresampled(
            $square,
            $source,
            0,
            0,
            intdiv($width - $side, 2),
            intdiv($height - $side, 2),
            self::SIZE,
            self::SIZE,
            $side,
            $side,
        );

        return $square;
    }

    private function encode(callable $write): string
    {
        ob_start();
        $write();

        return ob_get_clean();
    }
}
