<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Gumlet\ImageResize;

class CardService {

    public function __construct(
        protected SluggerInterface $slugger,
        protected string $imagesDirectory
    ){

    }

    public function attachImageToCard($image, $card){
        if(!$image) {
            return;
        }

        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

        // Get template width
        $width = $card->getTemplate()->getImageWidth();

        // Upscale for cleaner resolution
        $width *= 2;

        // Resize the image if necessary
        $imageResize = new ImageResize($image->getPathname());
        $imageResize->resizeToWidth($width);
        $imageResize->save($this->imagesDirectory . '/' . $newFilename);

        // Set the image path in the entity
        $relativePath = 'uploads/images/' . $newFilename;
        $card->setCardImage($relativePath);
    }

}