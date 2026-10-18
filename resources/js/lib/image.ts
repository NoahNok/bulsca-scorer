/**
 * Shrink a photo in the browser before uploading it, so a multi-megabyte phone
 * photo goes over pool wifi as a ~60 KB JPEG. EXIF rotation is applied. Falls
 * back to the original file if the browser can't decode it (the server still
 * validates and re-encodes whatever arrives).
 */
export async function downscaleImage(
    file: File,
    maxSide = 512,
    quality = 0.85,
): Promise<Blob> {
    try {
        const bitmap = await createImageBitmap(file, {
            imageOrientation: "from-image",
        });
        const scale = Math.min(1, maxSide / Math.max(bitmap.width, bitmap.height));

        const canvas = document.createElement("canvas");
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);

        const context = canvas.getContext("2d");
        if (!context) return file;

        // transparent PNGs go onto white rather than black
        context.fillStyle = "#fff";
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close();

        const blob = await new Promise<Blob | null>((resolve) =>
            canvas.toBlob(resolve, "image/jpeg", quality),
        );

        return blob && blob.size < file.size ? blob : file;
    } catch {
        return file;
    }
}
