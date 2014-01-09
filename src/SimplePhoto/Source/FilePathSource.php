<?php namespace SimplePhoto\Source;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class FilePathUploadSource implements PhotoSourceInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * {@inheritDocs}
     */
    public function process($photoData)
    {
        $this->file = $photoData;
    }

    /**
     * {@inheritDocs}
     */
    public function getName()
    {
        return basename($this->file);
    }

    /**
     * {@inheritDocs}
     */
    public function getFile()
    {
        return $this->file;
    }
}
