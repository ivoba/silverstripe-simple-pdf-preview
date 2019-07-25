<?php

namespace Ivoba\SilverStripe\SimplePdfPreview\tests;

use Ivoba\SilverStripe\SimplePdfPreview\SimplePdfPreviewImagickGenerator;
use SilverStripe\Dev\SapphireTest;

class SimplePdfPreviewImagickGeneratorTest extends SapphireTest
{
    protected $saveTo = __FILE__.'/pdf.jpg';

    public function testGenerate()
    {
        $generator = new SimplePdfPreviewImagickGenerator();

        $check = $generator->generatePreviewImage(__FILE__.'/pdf-test.pdf', $this->saveTo);

        $this->assertInstanceOf(SimplePdfPreviewImagickGenerator::class, $generator);
        $this->assertTrue($check);
        $this->assertTrue(file_exists($this->saveTo));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unlink($this->saveTo);
    }

}
