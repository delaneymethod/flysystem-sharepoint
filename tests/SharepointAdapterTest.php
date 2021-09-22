<?php

namespace DelaneyMethod\FlysystemSharepoint\Test;

use Prophecy\Argument;
use League\Flysystem\Config;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use DelaneyMethod\Sharepoint\Client;
use DelaneyMethod\Sharepoint\Exceptions\BadRequest;
use DelaneyMethod\FlysystemSharepoint\SharepointAdapter;

class SharepointAdapterTest extends TestCase
{
	/** @var \DelaneyMethod\Sharepoint\Client|\Prophecy\Prophecy\ObjectProphecy */
	protected $client;

	/** @var \DelaneyMethod\FlysystemSharepoint\SharepointAdapter */
	protected $sharepointAdapter;

	public function setUp(): void
	{
		$this->client = $this->prophesize(Client::class);

		$this->sharepointAdapter = new SharepointAdapter($this->client->reveal(), 'prefix');
	}

	/** @test */
	public function it_can_get_a_client()
	{
		$this->assertInstanceOf(Client::class, $this->sharepointAdapter->getClient());
	}
}
