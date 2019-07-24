<?php

namespace Ivoba\SilverStripe\SimplePdfPreview;

use SilverStripe\Control\Director;

class SimplePdfPreviewImagickGenerator implements SimplePdfPreviewGeneratorInterface
{

    public function generatePreviewImage($pdfFile, $saveTo)
    {
        try {
            $img = new \imagick(Director::getAbsFile($pdfFile) . "[0]"); //we only take first page

            // -flatten option, this is necessary for images with transparency, it will produce white background for transparent regions
            $img->setImageAlphaChannel(11);//Imagick::ALPHACHANNEL_REMOVE has been added in 3.2.0b2
            $img->mergeImageLayers(\imagick::LAYERMETHOD_FLATTEN);

            //set new format
            //@Todo detect format from filename
            $img->setImageFormat('jpg');
            $img->setCompressionQuality(100);

            //save image file
            $img->writeImages($saveTo, false);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

}
