<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\AccessibilityLog;
use App\Models\Audio;
use App\Models\Conversation;
use App\Models\Emergency;
use App\Models\Image;
use App\Models\MedicalRecord;
use App\Models\Message;
use App\Models\SignLanguage;
use App\Models\User;
use App\Observers\EmergencyObserver;
use App\Observers\MessageObserver;
use App\Observers\UserObserver;
use App\Policies\AccessibilityLogPolicy;
use App\Policies\AudioPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\EmergencyPolicy;
use App\Policies\ImagePolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\MessagePolicy;
use App\Policies\SignLanguagePolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        AccessibilityLog::class  => AccessibilityLogPolicy::class,
        Audio::class             => AudioPolicy::class,
        Conversation::class      => ConversationPolicy::class,
        Emergency::class         => EmergencyPolicy::class,
        Image::class             => ImagePolicy::class,
        MedicalRecord::class     => MedicalRecordPolicy::class,
        Message::class           => MessagePolicy::class,
        SignLanguage::class      => SignLanguagePolicy::class,
        User::class              => UserPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();
        User::observe(UserObserver::class);
        Message::observe(MessageObserver::class);
        Emergency::observe(EmergencyObserver::class);
    }
}
