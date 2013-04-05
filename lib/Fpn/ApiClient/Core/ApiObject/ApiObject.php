<?php

namespace Fpn\ApiClient\Core\ApiObject;

use Fpn\ApiClient\Core\ApiObject\ApiObjectInterface;
use Fpn\ApiClient\Core\ApiClient;
use Fpn\ApiClient\Core\Utility\Caster;

class ApiObject implements ApiObjectInterface
{
    protected $fetchUrl;
    protected $fetchAllUrl;
    protected $createUrl;
    protected $updateUrl;
    protected $deleteUrl;

    protected $apiClient;

    public function __construct($id = null)
    {
        $this->apiClient = new ApiClient();

        if (null !== $id) {
            $this->fetch($id);
        }
    }

    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    public function fetch($id)
    {
        $response = $this->apiClient->request('GET', sprintf($this->fetchUrl, $id));

        Caster::cast($response, $this);

        return $this;
    }

    public function fetchAll($page = 1, $limit = 20)
    {
        $items = $this->apiClient->request('GET', $this->fetchAllUrl)['items'];

        $response = array();
        foreach ($items as $item) {
            $currentClass = get_called_class();
            $castedObject = new $currentClass();

            Caster::cast($item, $castedObject);

            $response[] = $castedObject;
        }

        return $response;
    }

    public function save($datas = array())
    {
        if (!empty($this->id)) {
            $method = 'PUT';
            $url    = sprintf($this->updateUrl, $this->id);
        } else {
            $method = 'POST';
            $url    = $this->createUrl;
        }

        $response = $this->apiClient->request($method, $url, $datas);

        Caster::cast($response, $this);

        return $this;
    }

    public function delete()
    {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('Cannot delete an unset element.');
        }

        $response = $this->apiClient->request('DELETE', sprintf($this->deleteUrl, $this->id));

        Caster::cast($response, $this);

        return $this;
    }
}