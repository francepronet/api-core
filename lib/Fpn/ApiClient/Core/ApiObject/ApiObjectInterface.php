<?php

namespace Fpn\ApiClient\Core\ApiObject;

interface ApiObjectInterface
{
    public function fetch($id);
    public function fetchAll($page = 1, $limit = 20);
    public function delete();

    protected function saveItem($datas = array());
}
