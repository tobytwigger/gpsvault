<?php

namespace App\Services\Integrations;

use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Auth;

abstract class Integration implements Arrayable, Jsonable
{

    abstract public function id(): string;

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'service_url' => $this->serviceUrl(),
            'name' => $this->name(),
            'description' => $this->description(),
            'functionality' => $this->functionality(),
            'connected' => $this->connected(Auth::user()),
            'login_url' => $this->loginUrl()
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    abstract public function serviceUrl(): string;

    abstract public function name(): string;

    abstract public function description(): string;

    abstract public function functionality(): array;

    abstract public function connected(User $user): bool;

    public static function registerIntegration(string $key, string $class): void
    {
        $bindKey = sprintf('integrations.%s', $key);
        app()->bind($bindKey, $class);
        app()->tag([$bindKey], 'integrations');
    }

    abstract public function loginUrl(): string;

    abstract public function disconnect(User $user): void;

}
