<?php

namespace App\Observers;

use App\Models\Release;
use App\Models\ReleaseItem;

class ReleaseItemObserver
{
    /**
     * Handle the release item "created" event.
     *
     * @param \App\Models\ReleaseItem $releaseItem
     *
     * @return void
     */
    public function created(ReleaseItem $releaseItem)
    {
        switch (true) {
            case $releaseItem->name === ReleaseItem::CONFIG_KEY_CODE_COMMIT:
                $releaseItem->release->status = Release::RELEASE_STATUS_UNCOMMITTED;
                $releaseItem->release->save();
                break;
            case $releaseItem->name === ReleaseItem::CONFIG_KEY_AUDIT:
                $releaseItem->release->status = Release::RELEASE_STATUS_AUDITING;
                $releaseItem->release->save();
                break;
        }
    }

    /**
     * Handle the release item "updated" event.
     *
     * @param \App\Models\ReleaseItem $releaseItem
     *
     * @return void
     */
    public function updated(ReleaseItem $releaseItem)
    {
        switch (true){
            case $releaseItem->name === ReleaseItem::CONFIG_KEY_CODE_COMMIT:
                if($releaseItem->status === ReleaseItem::STATUS_SUCCESS){
                    $releaseItem->release->status = Release::RELEASE_STATUS_COMMITTED;
                    $releaseItem->release->save();
                }
                if($releaseItem->status === ReleaseItem::STATUS_FAILED){
                    $releaseItem->release->status = Release::RELEASE_STATUS_COMMIT_FAILED;
                    $releaseItem->release->save();
                }
                break;
            case $releaseItem->name === ReleaseItem::CONFIG_KEY_AUDIT:
                if($releaseItem->status === ReleaseItem::STATUS_FAILED){
                    $releaseItem->release->status = Release::RELEASE_STATUS_AUDIT_SUCCESS;
                    $releaseItem->release->save();
                }
                if($releaseItem->status === ReleaseItem::STATUS_FAILED){
                    $releaseItem->release->status = Release::RELEASE_STATUS_AUDIT_FAILED;
                    $releaseItem->release->save();
                }
                break;
        }
    }
}
