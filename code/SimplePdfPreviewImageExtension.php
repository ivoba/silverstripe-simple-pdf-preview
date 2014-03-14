<?php

class SimplePdfPreviewImageExtension extends DataExtension
{

    private $generator;
    private $folderToSave;
    private $imagePrefix;

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


    /*
     * for different Image generation engine
     * we could use $this->extend('generatePreviewImage', $foo);
     *
     * @Todo actually preview generation could happen at upload time
     */

    public function getPreviewImage()
    {
        $pdfFile = Director::getAbsFile($this->owner->getFileName());
        $pathInfo = pathinfo($pdfFile);
        if (strtolower($pathInfo['extension']) != 'pdf') {
            //@Todo if dev then exception?
            //prod fail silently
            return null;
        }
        $fileName = $pathInfo['filename'];

        $savePath = __DIR__ . '/../../../';
        $saveImage = $this->imagePrefix . $fileName . '.jpg';

        // Fix illegal characters
        $filter = FileNameFilter::create();
        $saveImage = $filter->filter($saveImage);
        $saveTo = $savePath . $this->folderToSave . $saveImage;
        $image = DataObject::get_one('Image', "`Name` = '{$saveImage}'");
        _SDG($this->folderToSave);
        _SD($this->imagePrefix);
        _SD($image);
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
     * @param null $folderToSave
     */
    public function setFolderToSave($folderToSave)
    {
        $this->folderToSave = $folderToSave;
    }

    /**
     * @param \SimplePdfPreviewGeneratorInterface $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param null $imagePrefix
     */
    public function setImagePrefix($imagePrefix)
    {
        $this->imagePrefix = $imagePrefix;
    }

}