<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\BrandBasePushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class WebPush implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private BrandBasePushNotification $notification;
    private array $targetRoles;
    private bool $sendToAdmin;

    /**
     * Create a new job instance.
     */
    public function __construct(BrandBasePushNotification $notification, array|string $targetRoles = ['admin'], $sendToAdmin = true)
    {
        $this->notification = $notification;
        if (!is_array($targetRoles)) {
            $this->targetRoles = [$targetRoles];
        } else {
            $this->targetRoles = $targetRoles;
        }
        $this->sendToAdmin = $sendToAdmin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {




        $brand = $this->notification->getCompetition()->getBrand;
        if ($brand == null && $this->sendToAdmin) {
            Notification::send(User::where('admin', true)->get(), $this->notification);
            return;
        }



        $targetBrandUsers = $brand->getUsers()->whereIn('role', $this->targetRoles)->get();


        if ($this->sendToAdmin) {
            $targetBrandUsers = $targetBrandUsers->merge(User::where('admin', true)->get());
        }

        // Also send it to the comp organiser
        $targetBrandUsers = $targetBrandUsers->push($this->notification->getCompetition()->getUser);


        $targetBrandUsers = $targetBrandUsers->unique();

        Notification::send($targetBrandUsers, $this->notification);
    }
}
