<?php

namespace App\Listeners;

use App\Models\Subscribe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use function PHPUnit\Framework\isEmpty;

class CinetPayIPNListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $payment_data = $event->payment_data;
        $cpm_custom = explode('-', $event->data['cpm_custom']);

        if (!isEmpty($cpm_custom)) {
            $user_id = $cpm_custom[0];
            $course_id = $cpm_custom[1];
            $subscribe_type = $cpm_custom[2];

            if ($payment_data["code"] == '00') {
                Subscribe::create([
                    'sold' => 4,
                    'user_id' => $user_id,
                    'course_id' => $course_id,
                    'subscribe_type' => $subscribe_type,
                ]);
            }
        }


    }
}
