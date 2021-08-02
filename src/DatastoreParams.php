<?php

declare(strict_types=1);

namespace Samuelnogueira\CacheDatastoreNewrelic;

use function array_filter;

/**
 * @api
 */
final class DatastoreParams
{
    /** @var string */
    private $product;
    /** @var string|null */
    private $collection;
    /** @var string|null */
    private $host;
    /** @var string|null */
    private $portPathOrId;
    /** @var string|null */
    private $databaseName;
    /** @var string|null */
    private $inputQueryLabel;

    /**
     * @param string      $product         The name of the datastore product being used: for example, MySQL to indicate
     *                                     that the segment represents a query against a MySQL database.
     * @param string|null $collection      The table or collection being used or queried against.
     * @param string|null $host            The datastore host name.
     * @param string|null $portPathOrId    The port or socket used to connect to the datastore.
     * @param string|null $databaseName    The database name or number in use.
     * @param string|null $inputQueryLabel The name of the ORM in use (for example: Doctrine).
     *
     * @see https://docs.newrelic.com/docs/agents/php-agent/php-agent-api/newrelic_record_datastore_segment/#parameters
     */
    public function __construct(
        string $product,
        ?string $collection = null,
        ?string $host = null,
        ?string $portPathOrId = null,
        ?string $databaseName = null,
        ?string $inputQueryLabel = null
    ) {
        $this->product         = $product;
        $this->collection      = $collection;
        $this->host            = $host;
        $this->portPathOrId    = $portPathOrId;
        $this->databaseName    = $databaseName;
        $this->inputQueryLabel = $inputQueryLabel;
    }

    /**
     * @return array<string, string>
     */
    public function asSegmentParams(?string $operation = null): array
    {
        return array_filter(
            [
                'product'         => $this->product,
                'collection'      => $this->collection,
                'operation'       => $operation,
                'host'            => $this->host,
                'portPathOrId'    => $this->portPathOrId,
                'databaseName'    => $this->databaseName,
                'inputQueryLabel' => $this->inputQueryLabel,
            ],
            static function (?string $value): bool {
                return $value !== null;
            },
        );
    }
}
