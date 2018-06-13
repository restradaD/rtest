<?php

namespace AppBundle\Pagination;

/**
 * Class PaginatedCollection
 * @package AppBundle\Pagination
 */
class PaginatedCollection
{
    /**
     * @var int $code
     */
    private $code;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var array $metadata
     */
    private $metadata;

    /**
     * @var array $items
     */
    public $recordset;

    /**
     * PaginatedCollection constructor.
     * @param array $recordset
     * @param int $totalItems
     */
    public function __construct(array $recordset = [], $totalItems = 0)
    {
        $code = ($totalItems > 0) ? 200 : 404;
        $message = ($totalItems > 0) ? 'Successfully' : 'Not found.';
        $type = ($totalItems > 0) ? 'info' : 'warning';

        $this->code = $code;
        $this->message = $message;
        $this->type = $type;
        $this->recordset = $recordset;
        $this->metadata = ['total' => $totalItems, 'count' => count($recordset)];
    }

    /**
     * @param string $ref
     * @param string $url
     */
    public function addLink($ref, $url)
    {
        $this->metadata['links'][$ref] = $url;
    }
}