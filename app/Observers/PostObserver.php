<?php

namespace App\Observers;

use App\Models\Post;

use Illuminate\Support\Facades\Storage;

class PostObserver
{
    /**
     * Handle the post "deleted" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function deleted(Post $post)
    {
        $disk = Storage::disk('gcs');
        $dir = 'pages/' . $post->page_id . '/' . $post->id;

        if ($disk->files($dir)) { // verify if exists files inside object
            $disk->delete($disk->files($dir)); //delete all files and object in GCS
        }

        // $disk->deleteDirectory($dir);
    }
}
