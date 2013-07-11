<?php

namespace Fpn\ApiClient\Core\ApiObject;

use Fpn\ApiClient\Core\ApiObject\ApiObjectInterface;
use Fpn\ApiClient\Core\ApiClient;
use Fpn\ApiClient\Core\Utility\Caster;

abstract class ApiObject implements ApiObjectInterface
{
    protected $fetchUrl;
    protected $fetchAllUrl;
    protected $createUrl;
    protected $updateUrl;
    protected $deleteUrl;

    protected $apiClient;

    protected $id;

    abstract public function save();

    public function __construct()
    {
        $this->apiClient = new ApiClient();
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

    public function fetchAll($page = 1, $limit = 20, $query = null)
    {
        $queryString = "?page={$page}&limit={$limit}";
        if (!empty($query)) {
            $queryString .= '&'.http_build_query($query);
        }

        $items = $this->apiClient->request('GET', $this->fetchAllUrl.$queryString)->items;

        $response = array();
        foreach ($items as $item) {
            $item = Caster::arrayToStdObject($item);
            $currentClass = get_called_class();
            $castedObject = new $currentClass();

            Caster::cast($item, $castedObject);

            $response[] = $castedObject;
        }

        return $response;
    }

    protected function saveItem($datas = array())
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

    public function getId()
    {
        return $this->id;
    }
}
