<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use App\Models\Course;
use App\Models\Product;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class AnyOneController extends Controller
{
    /**
     * Get New
     *
     * @return array
     */
    public function getNews(News $news): array
    {
        $category = [];

        foreach (explode(',', $news->category) as $item) {
            $category[] = ucfirst(trim($item));
        }

        return [
            'id' => $news->id,
            'title' => $news->title,
            'image' => $news->image,
            'category' => $category,
            'author' => $news->author,
            'created_at' => $news->created_at,
            'description' => $news->description,
        ];
    }

    /**
     * Get All News
     *
     * @return Collection
     */
    public function getAllNews(): Collection
    {
        return News::orderBy('created_at', 'desc')->get()->map(function (News $new) {
            $category = [];

            foreach (explode(',', $new->category) as $item) {
                $category[] = ucfirst(trim($item));
            }

            return [
                'id' => $new->id,
                'title' => $new->title,
                'image' => $new->image,
                'category' => $category,
                'author' => $new->author,
                'created_at' => $new->created_at,
                'description' => $new->description,
            ];
        });
    }

    public function getAllProducts(): Collection
    {
        return Product::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get All Courses
     *
     * @return Collection
     */
    public function getAllCourses(): Collection
    {
        return Course::orderBy('created_at', 'desc')->get();
    }
}
