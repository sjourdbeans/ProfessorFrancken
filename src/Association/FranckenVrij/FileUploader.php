<?php

declare(strict_types=1);

namespace Francken\Association\FranckenVrij;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploader;

final class FileUploader
{
    private const ONE_HUNDRED_MB = 100 * 1024 * 1024;

    /**
     * @var MediaUploader
     */
    private $uploader;

    public function __construct(MediaUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadPdf(Request $request, Edition $edition) : void
    {
        /** @var Media */
        $franckenVrijMedia = $this->uploader->fromSource($request->file('pdf'))
            ->toDirectory('francken-vrij')
            ->useFilename($edition->volume . '-' . $edition->edition)
            ->setMaximumSize(self::ONE_HUNDRED_MB)
            ->upload();

        $coverMedia = $this->generateCoverMedia(
            $request,
            $edition,
            $franckenVrijMedia
        );

        $edition->cover = $coverMedia->getUrl();
        $edition->pdf = $franckenVrijMedia->getUrl();
    }

    private function generateCoverMedia(
        Request $request,
        Edition $edition,
        Media $franckenVrijMedia
    ) : Media {
        /** @var string|UploadedFile */
        $cover_file = $request->hasFile('cover')
            ? $request->file('cover')
            : $this->generateCoverImageFromPdf(
                $franckenVrijMedia->getAbsolutePath()
            );

        return $this->uploader->fromSource($cover_file)
            ->toDirectory('francken-vrij/covers/')
            ->useFilename($edition->volume . '-' . $edition->edition . '-cover')
            ->setMaximumSize(self::ONE_HUNDRED_MB)
            ->upload();
    }

    private function generateCoverImageFromPdf(string $pdf_path) : string
    {
        $cover_path = preg_replace('"\.pdf$"', '-cover.png', $pdf_path);

        $imagick = new \Imagick();
        $imagick->setCompressionQuality(100);
        $imagick->setResolution(300, 300);
        $imagick->readImage($pdf_path . '[0]');
        $imagick->resizeImage(175, 245, \Imagick::FILTER_LANCZOS, 0.9);
        $imagick->transformImageColorspace(\Imagick::COLORSPACE_SRGB);
        $imagick->setFormat('png');
        $imagick->writeImage($cover_path);

        return $cover_path;
    }
}