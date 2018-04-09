<?php

namespace DelaneyMethod\FlysystemSharepoint;

use Exception;
use League\Flysystem\Config;
use League\Flysystem\Util\MimeType;
use DelaneyMethod\Sharepoint\Client;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

class SharepointAdapter extends AbstractAdapter
{
	use NotSupportingVisibilityTrait;

	protected $client;

	public function __construct(Client $client, string $prefix = '')
	{
		$this->client = $client;
		
		$this->setPathPrefix($prefix);
	}
	
	public function write($path, $contents, Config $config)
	{
		return $this->upload($path, $contents);
	}
	
	public function writeStream($path, $resource, Config $config)
	{
		return $this->upload($path, $resource);
	}
	
	public function update($path, $contents, Config $config)
	{
		return $this->upload($path, $contents);
	}
	
	public function updateStream($path, $resource, Config $config)
	{
		return $this->upload($path, $resource);
	}
	
	public function read($path)
	{
		if (!$response = $this->readStream($path)) {
			return false;
		}
		
		$response['contents'] = stream_get_contents($response['response']);
		
		fclose($response['response']);
		
		unset($response['response']);
		
		return $response;
	}
	
	public function readStream($path)
	{
		$path = $this->applyPathPrefix($path);
		
		$response = $this->client->download($path);
		
		return compact('response');
	}
	
	protected function upload($path, $contents)
	{
		$path = $this->applyPathPrefix($path);
		
		$response = $this->client->upload($path, $contents);
		
		return $this->normalizeResponse($response);
	}
	
	public function rename($path, $newPath) : bool
	{
		$path = $this->applyPathPrefix($path);
		
		$mimeType = $this->getMimetype($path);
		
		$newPath = $this->applyPathPrefix($newPath);
		
		return $this->client->rename($path, $newPath, $mimeType);
	}
	
	public function copy($path, $newpath) : bool
	{
		$path = $this->applyPathPrefix($path);
		
		$newpath = $this->applyPathPrefix($newpath);
		
		return $this->client->copy($path, $newpath, $mimeType);
	}
	
	public function delete($path) : bool
	{
		$path = $this->applyPathPrefix($path);
		
		return $this->client->delete($path);
	}
	
	public function deleteDir($dirname) : bool
	{
		return $this->delete($dirname);
	}
	
	public function createDir($path, Config $config) : bool
	{
		$path = $this->applyPathPrefix($path);
		
		return $this->client->createFolder($path);
	}
	
	public function has($path) : bool
	{
		try {
			$path = $this->applyPathPrefix($path);
			
			$mimeType = $this->getMimetype($path);
			
			$metadata = $this->client->getMetadata($path, $mimeType);
		
			return true;
		} catch (Exception $exception) {
			return false;
		}
	}
	
	public function listContents($path = '', $recursive = false) : array
	{
		$path = $this->applyPathPrefix($path);
			
		$folders = $this->client->listFolder($path, $recursive);
			
		if (count($folders) === 0) {
			return [];
		}
		
		$allFolders = [];
		
		foreach ($folders as $folder) {
			$object = $this->normalizeResponse($folder);
		
			array_push($allFolders, $object);
			
			if ($recursive) {
				$allFolders = array_merge($allFolders, $this->listContents($folder['Name'], true));
			}
		}
		
		return $allFolders;
	}
	
	public function getMetadata($path)
	{
		$path = $this->applyPathPrefix($path);
		
		$mimeType = $this->getMimetype($path);
		
		$metadata = $this->client->getMetadata($path, $mimeType);
		
		return $this->normalizeResponse($metadata);
	}
	
	public function getSize($path)
	{
		return $this->getMetadata($path);
	}
	
	public function getMimetype($path)
	{
		return ['mimetype' => MimeType::detectByFilename($path)];
	}
	
	public function getTimestamp($path)
	{
		return $this->getMetadata($path);
	}
	
	public function applyPathPrefix($path) : string
	{
		$path = parent::applyPathPrefix($path);

		return '/'.trim($path, '/');
	}

	public function getClient() : Client
	{
		return $this->client;
	}
	
	protected function normalizeResponse(array $response) : array
	{
		list($normalizedPathPart1, $normalizedPathPart2) = explode('Shared Documents', $response['ServerRelativeUrl']);
		
		$normalizedPath = ltrim($this->removePathPrefix($normalizedPathPart2), '/');
	
		$normalizedResponse = [
			'path' => $normalizedPath
		];
		
		if (isset($response['TimeLastModified'])) {
			$normalizedResponse['timestamp'] = strtotime($response['TimeLastModified']);
		} elseif (isset($response['Modified'])) {
			$normalizedResponse['timestamp'] = strtotime($response['Modified']);
		}
		
		if (isset($response['size'])) {
			$normalizedResponse['size'] = $response['size'];
			
			$normalizedResponse['bytes'] = $response['size'];
		}
		
		$type = ($response['__metadata']['type'] === 'SP.Folder' ? 'dir' : 'file');
		
		$normalizedResponse['type'] = $type;
		
		return $normalizedResponse;
	}
}
