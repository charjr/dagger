<?php

namespace Dagger\Client;

use Dagger\GraphQl\QueryBuilderChain;
use GraphQL\QueryBuilder\QueryBuilder;

abstract class AbstractObject
{
    //todo remove this line once done debugging
    public $lastQuery;

    public function __construct(
        protected readonly AbstractClient $client,
        protected readonly QueryBuilderChain $queryBuilderChain,
    ) {}

    protected function queryLeaf(QueryBuilder $leafQueryBuilder, string $leafKey): array|string|int|float|bool|null
    {
        $queryBuilderChain = $this->queryBuilderChain->chain($leafQueryBuilder);

        // todo remove this line once done debugging
        $this->lastQuery = (string) $queryBuilderChain->getFullQuery();

        return $this->client->queryLeaf($queryBuilderChain->getFullQuery(), $leafKey);
    }
}
