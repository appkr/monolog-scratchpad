<?php

namespace App\LogHandlers;

use Elasticsearch\Client as ESClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class ESHandler extends AbstractProcessingHandler
{
    /**
     * @var ESClient
     */
    private $esClient;

    public function __construct(ESClient $esClient, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->esClient = $esClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record = [])
    {
        $payload = [
            'refresh' => true,
            'body' => [
                [
                    'index' => [
                        '_index' => 'monolog',
                        '_type' => config('app.name')
                    ]
                ],
                $record['formatted'],
            ]
        ];

        $this->esClient->bulk($payload);
    }
}