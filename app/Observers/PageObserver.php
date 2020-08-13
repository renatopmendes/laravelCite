<?php

namespace App\Observers;

use App\Models\Page;

use Illuminate\Support\Facades\Storage;

class PageObserver
{
    /**
     * Handle the page "created" event.
     *
     * @param  \App\Page  $page
     * @return void
     */
    public function created(Page $page)
    {
        $page->fcm_topic = substr(md5(uniqid(rand(), true)), 0, 10);
        $page->save();
    }

    /**
     * Handle the page "updated" event.
     *
     * @param  \App\Page  $page
     * @return void
     */
    public function updated(Page $page)
    {
        //
    }

    /**
     * Handle the page "deleted" event.
     *
     * @param  \App\Page  $page
     * @return void
     */
    public function deleted(Page $page)
    {
        $disk = Storage::disk('gcs');
        $dir = 'pages/' . $page->id;

        if ($disk->files($dir)) { // verify if exists files inside object
            $disk->delete($disk->files($dir)); //delete all files and object in GCS
        }

        // $disk->deleteDirectory($dir);
    }

    /**
     * Handle the page "restored" event.
     *
     * @param  \App\Page  $page
     * @return void
     */
    public function restored(Page $page)
    {
        //
    }

    /**
     * Handle the page "force deleted" event.
     *
     * @param  \App\Page  $page
     * @return void
     */
    public function forceDeleted(Page $page)
    {
        //
    }
}
