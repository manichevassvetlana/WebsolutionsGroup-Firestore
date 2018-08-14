<?php

namespace WebsolutionsGroup\Firestore;

class FieldPath extends \Google\Cloud\Firestore\FieldPath
{
    private $fieldNames;

    /**
     * @param array $fieldNames A list of field names.
     */
    public function __construct(array $fieldNames)
    {
        $this->fieldNames = $fieldNames;
    }

    /**
     * Get the path elements.
     *
     * @access private
     * @return array
     */
    public function path()
    {
        return $this->fieldNames;
    }

    /**
     * Create a FieldPath from a string path.
     *
     * @param string $path
     * @return FieldPath
     * @access private
     */
    public static function fromString($path)
    {
        return new self(explode('.', $path));
    }
}