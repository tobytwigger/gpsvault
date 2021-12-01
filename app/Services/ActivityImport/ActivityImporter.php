<?php

namespace App\Services\ActivityImport;

use App\Exceptions\ActivityDuplicate;
use App\Models\Activity;
use App\Models\AdditionalActivityData;
use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityImporter
{

    private ?User $user = null;

    private Activity $existingActivity;

    private ActivityDetails $activityDetails;

    public function __construct(?User $user = null)
    {
        $this->user = $user ?? Auth::user();
        $this->activityDetails = new ActivityDetails();
    }

    public static function update(Activity $activity): ActivityImporter
    {
        $user = $activity->user;
        $instance = (new static($user))->setExistingActivity($activity);
        if($activity->name) {
            $instance->withName($activity->name);
        }
        if($activity->description) {
            $instance->withDescription($activity->description);
        }
        if($activity->activityFile) {
            $instance->withActivityFile($activity->activityFile);
        }
        if($activity->additionalActivityData()->exists()) {
            $instance->activityDetails()->setAdditionalData(
                $activity->additionalActivityData()->get()->mapWithKeys(fn(AdditionalActivityData $data) => [$data->key => $data->value])->all()
            );
        }
        if($activity->files()->exists()) {
            $instance->activityDetails()->setMedia(
                $activity->files()->get()->all()
            );
        }
        if($activity->linked_to) {
            $instance->activityDetails()->setLinkedTo($activity->linked_to);
        }
        return $instance;
    }

    public function withDistance(int $distance): ActivityImporter
    {
        $this->activityDetails->setDistance($distance);
        return $this;
    }

    public function startedAt(Carbon $startedAt): ActivityImporter
    {
        $this->activityDetails->setStartedAt($startedAt);
        return $this;
    }

    public function activityDetails(): ActivityDetails
    {
        return $this->activityDetails;
    }

    public static function for(User $user): ActivityImporter
    {
        return new static($user);
    }

    public function setExistingActivity(Activity $activity): ActivityImporter
    {
        $this->existingActivity = $activity;
        return $this;
    }

    public function withName(?string $name = null): ActivityImporter
    {
        if($name !== null) {
            $this->activityDetails->setName($name);
        }
        return $this;
    }

    public function withDescription(?string $description = null): ActivityImporter
    {
        if($description !== null) {
            $this->activityDetails->setDescription($description);
        }
        return $this;
    }

    public function withActivityFile(File $file): ActivityImporter
    {
        $this->activityDetails->setActivityFile($file);
        return $this;
    }

    public function withAdditionalData(string $key, mixed $value): ActivityImporter
    {
        $this->activityDetails->addAdditionalData($key, $value);
        return $this;
    }

    public function linkTo(string $integration): ActivityImporter
    {
        $this->activityDetails->addLinkedTo($integration);
        return $this;
    }

    public function import(): Activity
    {
        $this->checkForDuplication();
        return $this->saveActivityModel(new Activity());
    }

    public function save(): Activity
    {
        if($this->activityDetails->getActivityFile() !== null && $this->existingActivity->activity_file_id !== $this->activityDetails->getActivityFile()->id) {
            $this->checkForDuplication();
        }
        return $this->saveActivityModel($this->existingActivity);
    }

    private function saveActivityModel(Activity $activity): Activity
    {
        $activity->fill([
            'name' => $this->activityDetails->getName(),
            'description' => $this->activityDetails->getDescription(),
            'linked_to' => $this->activityDetails->getLinkedTo(),
            'activity_file_id' => $this->activityDetails->getActivityFile()?->id,
            'start_at' => $this->activityDetails->getStartedAt(),
            'distance' => $this->activityDetails->getDistance(),
            'user_id' => $this->user?->id ?? Auth::id() ?? throw new \Exception('A user could not be found to run the import against')
        ]);
        $activity->save();
        $activity->files()->sync(collect($this->activityDetails->getMedia())->pluck('id'));
        $activity->additionalActivityData()->whereNotIn('key', array_keys($this->activityDetails->getAdditionalData()))->delete();
        collect($this->activityDetails->getAdditionalData())
            ->each(fn($value, string $key) => $activity->addOrUpdateAdditionalData($key, $value));
        return $activity;
    }

    public function addMedia(array $files): ActivityImporter
    {
        $this->activityDetails->setMedia(array_merge(
            $this->activityDetails->getMedia(),
            $files
        ));
        return $this;
    }

    private function checkForDuplication()
    {
        // TODO

//        if($isDuplicate) {
//            throw new ActivityDuplicate($duplicatedActivity);
//        }
    }

}
