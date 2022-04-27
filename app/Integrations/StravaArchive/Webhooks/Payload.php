<?php

namespace App\Integrations\Strava\Webhooks;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Payload
{
    private string $objectType;

    private int $objectId;

    private string $aspectType;

    private array $updates;

    private int $ownerId;

    private int $subscriptionId;

    private Carbon $eventTime;

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @param string $objectType
     * @return Payload
     */
    public function setObjectType(string $objectType): Payload
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     * @return Payload
     */
    public function setObjectId(int $objectId): Payload
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAspectType(): string
    {
        return $this->aspectType;
    }

    /**
     * @param string $aspectType
     * @return Payload
     */
    public function setAspectType(string $aspectType): Payload
    {
        $this->aspectType = $aspectType;

        return $this;
    }

    /**
     * @return array
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }

    /**
     * @param array $updates
     * @return Payload
     */
    public function setUpdates(array $updates): Payload
    {
        $this->updates = $updates;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     * @return Payload
     */
    public function setOwnerId(int $ownerId): Payload
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSubscriptionId(): int
    {
        return $this->subscriptionId;
    }

    /**
     * @param int $subscriptionId
     * @return Payload
     */
    public function setSubscriptionId(int $subscriptionId): Payload
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * @return Carbon
     */
    public function getEventTime(): Carbon
    {
        return $this->eventTime;
    }

    /**
     * @param Carbon $eventTime
     * @return Payload
     */
    public function setEventTime(Carbon $eventTime): Payload
    {
        $this->eventTime = $eventTime;

        return $this;
    }

    public function toArray()
    {
        return [
            'object_type' => $this->getObjectType(),
            'object_id' => $this->getObjectId(),
            'aspect_type' => $this->getAspectType(),
            'updates' => $this->getUpdates(),
            'owner_id' => $this->getOwnerId(),
            'subscription_id' => $this->getSubscriptionId(),
            'event_time' => $this->getEventTime()->getTimestamp(),
        ];
    }

    public static function createFromRequest(Request $request): Payload
    {
        $instance = new static();
        $instance->setObjectType($request->input('object_type'));
        $instance->setObjectId($request->input('object_id'));
        $instance->setAspectType($request->input('aspect_type'));
        $instance->setUpdates($request->input('updates', []));
        $instance->setOwnerId($request->input('owner_id'));
        $instance->setSubscriptionId($request->input('subscription_id'));
        $instance->setEventTime(Carbon::createFromFormat('U', $request->input('event_time')));

        return $instance;
    }

    public static function rules(): array
    {
        return [
            'object_type' => 'required|string|in:activity,athlete',
            'object_id' => 'required|integer',
            'aspect_type' => 'required|string|in:create,update,delete',
            'updates' => 'sometimes|array',
            'owner_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (!User::whereAdditionalData('strava_athlete_id', $value)->exists()) {
                    $fail('The ' . $attribute . ' has not requested webhooks.');
                }
            }],
            'subscription_id' => 'required|integer',
            'event_time' => 'required|date_format:U'
        ];
    }
}
