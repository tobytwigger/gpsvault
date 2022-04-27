<?php

namespace App\Integrations\Dropbox\Client;

use Illuminate\Contracts\Session\Session;
use Kunnu\Dropbox\Store\PersistentDataStoreInterface;

class PersistentDataStore implements PersistentDataStoreInterface
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function get($key)
    {
        return $this->session->get($key);
    }

    public function set($key, $value)
    {
        $this->session->put($key, $value);
    }

    public function clear($key)
    {
        $this->session->forget($key);
    }
}
