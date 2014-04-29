<?php

use Httpful\Mime;
use Httpful\Http;

class ResourceServiceClient extends AbstractServiceClient {

	protected function getServiceName() {
		return "resource-service";
	}

	public function __construct($_username, $_api_key, $_api_url, $_response_format, $_version) {
		parent::__construct($_username, $_api_key, $_api_url, $_response_format, $_version);
	}

	public function getResources($offset = 0) {
		$response = $this->getClient(Http::GET)->uri($this->getServiceUri() . "resources." . $this->_response_format
		. (!empty($offset) ? "?start=" . $offset : ""))->send();
		return $this->checkResponse($response);
	}

	public function getResource($resourceId) {
		$response = $this->getClient(Http::GET)->uri($this->getServiceUri() . "resources/" . $resourceId . "." . $this->_response_format)->send();
		return $this->checkResponse($response);
	}

	public function addResource($resourceName, $failedAttemptsBeforeLock) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "resources." . $this->_response_format)
		->body(array("resourceName" => $resourceName, "failedAttemptsBeforeLock" => $failedAttemptsBeforeLock))
		->send();
		return $this->checkResponse($response);
	}

	public function editResource($resourceId, $resourceName, $failedAttemptsBeforeLock) {
		$response = $this->getClient(Http::PUT)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "resources/" . $resourceId . "." . $this->_response_format)
		->body(array("resourceName" => $resourceName, "failedAttemptsBeforeLock" => $failedAttemptsBeforeLock))
		->send();
		return $this->checkResponse($response);
	}

	public function deleteResource($resourceId) {
		$response = $this->getClient(Http::DELETE)
		->uri($this->getServiceUri() . "resources/" . $resourceId . "." . $this->_response_format)->send();
		return $this->checkResponse($response);
	}

	public function getResourcesQuantity() {
		$response = $this->getClient(Http::GET)->uri($this->getServiceUri() . "resources/quantity." . $this->_response_format)->send();
		return $this->checkResponse($response);
	}

	public function assignUserToResource($resourceId, $userId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "assign/user." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "userId" => $userId))
		->send();
		return $this->checkResponse($response);
	}

	public function assignTokenToResource($resourceId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "assign/token." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

	public function assignUserAndTokenToResource($resourceId, $userId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "assign/user-token." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "userId" => $userId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

	public function assignTokenWithUserToResource($resourceId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "assign/token-with-user." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

	public function unassignUserFromResource($resourceId, $userId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "unassign/user." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "userId" => $userId))
		->send();
		return $this->checkResponse($response);
	}

	public function unassignTokenFromResource($resourceId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "unassign/token." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

	public function unassignUserAndTokenFromResource($resourceId, $userId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "unassign/user-token." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "userId" => $userId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

	public function unassignTokenWithUserFromResource($resourceId, $tokenId) {
		$response = $this->getClient(Http::POST)->sends(Mime::FORM)
		->uri($this->getServiceUri() . "unassign/token-with-user." . $this->_response_format)
		->body(array("resourceId" => $resourceId, "tokenId" => $tokenId))
		->send();
		return $this->checkResponse($response);
	}

}