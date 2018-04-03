<?php

namespace DelaneyMethod\FlysystemSharepoint;

use League\Flysystem\Config;
use League\Flysystem\Util\MimeType;
use DelaneyMethod\Sharepoint\Client;
use League\Flysystem\Adapter\AbstractAdapter;
use DelaneyMethod\Sharepoint\Exceptions\BadRequest;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

class SharepointAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    /** @var \Spatie\Dropbox\Client */
    protected $client;

    public function __construct(Client $client, string $prefix = '')
    {
        $this->client = $client;

        $this->setPathPrefix($prefix);
    }
	
	/**
     * {@inheritdoc}
     */
    public function applyPathPrefix($path) : string
    {
        $path = parent::applyPathPrefix($path);

        return '/'.trim($path, '/');
    }

    public function getClient() : Client
    {
        return $this->client;
    }
}
