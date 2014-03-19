<?php

class SimplePdfPreviewImagickGenerator implements SimplePdfPreviewGeneratorInterface
{

    public function generatePreviewImage($pdfFile, $saveTo)
    {
        try {
            $img = new imagick(Director::getAbsFile($pdfFile) . "[0]"); //we only take first page

            // -flatten option, this is necessary for images with transparency, it will produce white background for transparent regions
            $img = $img->flattenImages();

            //set new format
            //@Todo detect format from filename
            $img->setImageFormat('jpg');
            $img->setCompressionQuality(100);

            //save image file
            $img->writeImages($saveTo, false);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

} 