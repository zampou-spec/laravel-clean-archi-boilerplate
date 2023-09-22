<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Zampou\CinetPay\Facades\CinetPay;
use Illuminate\Contracts\Auth\Authenticatable;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * get Auth User
     *
     * @return Authenticatable
     */
    private function getAuthUser(): Authenticatable
    {
        return Auth::user();
    }

    /**
     * Get Subscribes
     *
     * @return Collection
     */
    public function getSubscribes(): Collection
    {
        $user = (object) $this->getAuthUser();

        return $user->subscribes()
            ->where('subscribe_type', 'classroom')
            ->get()
            ->map(function ($subscribe) {
                return [
                    'id' => $subscribe->id,
                    'sold' => $subscribe->sold,
                    'name' => $subscribe->course->name
                ];
            });
    }

    /**
     * Get Subscribe Courses
     *
     * @return Collection
     */
    public function getSubscribeCourses(): Collection
    {
        $user = (object) $this->getAuthUser();

        return Course::all()->map(function ($course) use ($user) {
            if ($user->role == 'admin') {
                $lock = false;
            } else if ($user->role == 'user') {
                $lock = !$user->subscribes()
                    ->where('course_id', $course->id)
                    ->where('subscribe_type', 'classroom')
                    ->exists();
            }

            return [
                'id' => $course->id,
                'lock' => $lock,
                'name' => $course->name,
                'image' => $course->image,
                'description' => $course->description,
            ];
        });
    }

    /**
     * Make Payment
     *
     * @return JsonResponse
     */
    public function makePayment(Course $course, $subscribe_type): JsonResponse
    {
        $user = (object) $this->getAuthUser();
        $transaction_id = Str::upper(Str::random(10));

        $transactionLink = CinetPay::generatePaymentLink([
            'currency' => 'XOF',
            'transaction_id' => $transaction_id,
            'customer_name' => $user->first_name,
            'customer_surname' => $user->last_name,
            'description' => 'Paiement de course de danse',
            'return_url' => env('FRONTEND_URL').'/dashboard',
            'metadata' => "{$user->id}-{$course->id}-{$subscribe_type}",
            'amount' => $subscribe_type === 'classroom' ? $course->price_classroom :  $course->price_online
        ]);

        if ($transactionLink['code'] == '201') {
            return response()->json(['link' => $transactionLink["data"]["payment_url"]]);
        }

        return response()->json([], 400);
    }
}
