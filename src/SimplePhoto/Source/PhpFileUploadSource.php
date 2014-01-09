<?php namespace SimplePhoto\Source;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class PhpFileUploadSource implements PhotoSourceInterface
{
    protected $fileData;

    /**
     * {@inheritDocs}
     */
    public function process($photoData)
    {
        $this->fileData = $photoData;
    }

    /**
     * {@inheritDocs}
     */
    public function getName()
    {
        return $this->fileData["name"];
    }

    /**
     * {@inheritDocs}
     */
    public function getFile()
    {
        return $this->fileData["tmp_name"];
    }
}
