<?php

class SimplePdfPreviewImageExtension extends DataExtension
{

    private $generator;
    private $folderToSave;
    private $imagePrefix;

//@Todo cant get Silverstripe DI to work with constructor injection
//    function __construct(SimplePdfPreviewGeneratorInterface $generator,
//                         $folderToSave = null,
//                         $imagePrefix = null,
//                         $savePath = null)
//    {
//        $this->generator = $generator;
//        $this->folderToSave = $folderToSave;
//        $this->imagePrefix = $imagePrefix;
//        $this->savePath = $savePath;
//    }

    public function getPdfPreviewImage()
    {
        $pdfFile = Director::getAbsFile($this->owner->getFileName());
        $pathInfo = pathinfo($pdfFile);
        if (strtolower($pathInfo['extension']) != 'pdf') {
            //@Todo if dev then exception? else fail silently
            return null;
        }
        $fileName = $pathInfo['filename'];

        $savePath = __DIR__ . '/../../../';
        $saveImage = $this->imagePrefix . '-' . $fileName . '.jpg';

        // Fix illegal characters
        $filter = FileNameFilter::create();
        $saveImage = $filter->filter($saveImage);
        $saveTo = $savePath . $this->folderToSave . $saveImage;

        $image = DataObject::get_one('Image', "`Name` = '{$saveImage}'");

        if (!$image) {
            $folderObject = DataObject::get_one("Folder", "`Filename` = '{$this->folderToSave}'");
            if ($folderObject) {
                if ($this->generator->generatePreviewImage($pdfFile, $saveTo)) {
                    $image = new Image();
                    $image->ParentID = $folderObject->ID;
                    $image->setName($saveImage);
                    $image->write();
                }
            }
        } else {
            //check LastEdited to update
            $cacheInValid = false;
            if (strtotime($image->LastEdited) < strtotime($this->owner->LastEdited)) {
                $cacheInValid = true;
            }
            if ($cacheInValid) {
                $this->generatePreviewImage($pdfFile, $saveTo);
            }
        }
        return $image;
    }

    /**
     * @param $folderToSave
     */
    public function setFolderToSave($folderToSave)
    {
        $this->folderToSave = $folderToSave;
    }

    /**
     * @param \SimplePdfPreviewGeneratorInterface $generator
     */
    public function setGenerator(\SimplePdfPreviewGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param $imagePrefix
     */
    public function setImagePrefix($imagePrefix)
    {
        $this->imagePrefix = $imagePrefix;
    }

}
