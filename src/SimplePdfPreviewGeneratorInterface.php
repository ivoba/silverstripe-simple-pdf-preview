<?php

namespace Ivoba\SilverStripe\SimplePdfPreview;

interface SimplePdfPreviewGeneratorInterface
{
    /**
     * @param $pdfFile
     * @param $saveTo
     * @return boolean
     */
    public function generatePreviewImage($pdfFile, $saveTo);
}
