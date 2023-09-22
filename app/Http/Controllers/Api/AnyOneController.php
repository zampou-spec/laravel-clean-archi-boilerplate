<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

class AnyOneController extends Controller
{

    /**
     * Get New
     *
     * @return array
     */
    public function getNew(News $new): array
    {
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
    }

    /**
     * Get All News
     *
     * @return Collection
     */
    public function getAllNews(): Collection
    {
        return News::all()->map(function (News $new) {
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

}
