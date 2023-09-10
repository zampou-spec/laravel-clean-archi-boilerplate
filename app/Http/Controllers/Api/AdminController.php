<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Subscribe;
use App\Models\Course;
use App\Models\Chapter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Alaouy\Youtube\Facades\Youtube;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Create a new AdminController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get All Users.
     *
     * @return Collection
     */
    public function getAllUser(): Collection
    {
        return User::all()->map(function ($user) {
            return [
                'id' => "$user->id",
                'name' => "$user->first_name $user->last_name",
                'email' => $user->email,
                'country' => $user->country,
                'mobile_number' => $user->mobile_number,
                'subscribes' => $user->subscribes->map(function ($subscribe) {
                    return [
                        'sold' => $subscribe->sold,
                        'name' => Str::lower($subscribe->course->name),
                        'type' => Str::lower($subscribe->subscribe_type),
                    ];
                })
            ];
        });
    }

    /**
     * Get Statistics for admin dashboard
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $userNumber = User::all()->count();
        $userActive = User::whereHas('subscribes')->count();
        $NumberActiveCourses = Course::whereHas('subscribes')->count();

        return [
            'user_number' => $userNumber,
            'user_active' => $userActive,
            'number_active_courses' => $NumberActiveCourses,
        ];
    }

    /**
     * Reduce sold for the classroom courses
     *
     * @return JsonResponse
     */
    public function removeSold(Request $request, User $user): JsonResponse
    {
        $subscribe = $user->subscribes()->where([
            'sold' => intval($request->sold),
            'subscribe_type' => $request->type
        ])->whereHas('course', function ($query) use ($request) {
            $query->where('name', $request->name);
        })->first();

        if ($subscribe and $subscribe->sold > 0) {
            $subscribe->update([
                'sold' => $subscribe ->sold - 1,
            ]);
        }

        return response()->json();
    }

    /**
     * Get All Courses
     *
     * @return Collection
     */
    public function getAllCourses(): Collection
    {
        return Course::all();
    }

    /**
     * Update Course
     *
     * @return JsonResponse
     */
    public function editCourse(Request $request, Course $course): JsonResponse
    {
        $courseData = $request->all();
        $image = $this->uploadFile($request);

        if ($image) {
            $isUpdate  = $course->update([
                ...$courseData,
                'image' => $image,
            ]);
        } else {
            $isUpdate  = $course->update($courseData);
        }

        if ($isUpdate)
            return response()->json();

        return response()->json([], 400);
    }

    /**
     * Create Course
     *
     * @return JsonResponse
     */
    public function createCourse(Request $request): JsonResponse
    {
        $courseData = $request->all();
        $image = $this->uploadFile($request);

        if ($image) {
            $isCreate = Course::create([
                ...$courseData,
                'image' => $image
            ]);
        }

        if ($isCreate)
            return response()->json();

        return response()->json([], 400);
    }

    /**
     * Delete Course
     *
     * @return bool
     */
    public function deleteCourse(Course $course): bool
    {
        $imageUrl = parse_url($course->image);
        $segment = explode('/', $imageUrl['path']);

        $imageName = end($segment);
        $storagePath  = storage_path('app/public/uploads/'.$imageName);

        if (file_exists($storagePath)) {
            unlink($storagePath);
        }

        return $course->delete();
    }

    /**
     * Get All Chapters
     *
     * @return Collection
     */
    public function getAllChapters(): Collection
    {
        return Chapter::all();
    }


    /**
     * Edit Chapter
     *
     * @return JsonResponse
     */
    public function editChapter(Request $request, Chapter $chapter): JsonResponse
    {
        $chapterData = $request->all();
        $isUpdate = $chapter->update($chapterData);

        if ($isUpdate)
            return response()->json();

        return response()->json([], 400);
    }

    /**
     * Create Chapter
     *
     * @return JsonResponse
     */
    public function createChapter(Request $request)
    {
        $chapterData = $request->all();
        $videos = $this->getPlaylistItemsByPlaylistId($request->playlist_id, true);

        if ($videos) {
            $isCreate = Chapter::create($chapterData);

            if ($isCreate)
                return response()->json();
        }

        return response()->json([], 400);
    }

    /**
     * Delete Chapter
     *
     * @return bool
     */
    public function deleteChapter(Chapter $chapter): bool
    {
        return $chapter->delete();
    }

    /**
     * Get Course Videos
     *
     * @return array | JsonResponse
     */
    public function getCourseVideos(Course $course, User $user): array | JsonResponse
    {
        $chapters = [
            'infos' => [
                'id' => $course->id,
                'title' => $course->name,
                'lock' => true,
            ],
            'chapters' => []
        ];

        if ($user->role == 'admin') {
            $chapters['infos']['lock'] = false;
        } else if ($user->role == 'user') {
            $chapters['infos']['lock'] = !$user->subscribes()
                ->where('course_id', $course->id)
                ->where('subscribe_type', 'classroom')
                ->exists();
        }

        foreach ($course->chapters as $chapter) {
            $videos = $this->getPlaylistItemsByPlaylistId($chapter->playlist_id,  $chapters['infos']['lock']);

            if ($videos) {
                $chapters['chapters'][] = [
                    'title' => $chapter->name,
                    'videos' => $videos
                ];
            } else {
                return response()->json([], 400);
            }
        }

        return $chapters;
    }

    /**
     * Static upload File
     *
     * @return bool | string
     */
    protected function uploadFile ($request): bool | string
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('uploads', $imageName, 'public');
            $imageLink = asset('storage/uploads/' . $imageName);
            return $imageLink;
        }

        return false;
    }

    /**
     * Get playlist items by playlistId from YouTube
     *
     * @return array | bool
     */
    protected function getPlaylistItemsByPlaylistId($id, $lock = false): array | bool
    {
        try {
            $youtubes = Youtube::getPlaylistItemsByPlaylistId($id);

            if ($youtubes['results']) {
                foreach ($youtubes['results'] as $youtube) {
                    $videos[] = [
                        'id' => str::random(10),
                        'lock' => $lock,
                        'title' => $youtube->snippet->title,
                        'image' =>  $youtube->snippet->thumbnails->maxres->url,
                        'video_url' => $lock ? null : 'https://youtu.be/'.$youtube->snippet->resourceId->videoId
                    ];
                }
            }
        } catch (\Exception $e) {
            return false;
        }

        return $videos;
    }

}
