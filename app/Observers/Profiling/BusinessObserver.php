<?php

namespace App\Observers\Profiling;

use App\Models\Business;

class BusinessObserver
{
    public function updateUserProfilingPercentage($business)
    {
        $parts = 0;
        $complete = 0;
        $unprofiled = [];
        #headquarters
        if ($business->headquarters_id != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Headquarters");
        }
        $parts += 1;
        #countries where active
        if ($business->countries()->exists()) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Countries where active");
        }
        $parts += 1;
        # main sector/industry
        if ($business->main_sector_id != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Main Sector");
        }
        $parts += 1;
        # other sectors/industries
        if ($business->otherSectors()->exists()) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Other Sectors");
        }
        $parts += 1;
        # incorporation_number
        if ($business->incorporation_number != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Incorporation Number");
        }
        $parts += 1;
        # company logo
        if ($business->logo != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Business Logo");
        }
        $parts += 1;
        # company banner
        if ($business->banner != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Business Banner");
        }
        $parts += 1;
        # certificate_of_incorporation
        if ($business->certificate_of_incorporation != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Business Certificate of Incorporation");
        }
        $parts += 1;
        # executive_summary
        if ($business->executive_summary != null) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Executive Summary");
        }
        $parts += 1;
        # business_interests
        if ($business->interests()->exists()) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Interests");
        }
        $parts += 1;
        # business_keywords
        if ($business->keywords()->exists()) {
            $complete += 1;
        }
        else{
            array_push($unprofiled, "Keywords");
        }
        $parts += 1;

        $percentage = $complete / $parts * 100;
        
        $business->profiling_percentage = $percentage;
        $business->unprofiled = $unprofiled;
        $business->saveQuietly();
    }

    /**
     * Handle the Business "created" event.
     *
     * @param  \App\Models\Business  $business
     * @return void
     */
    public function created(Business $business)
    {
        //
        // $this->updateUserProfilingPercentage($business);
    }

    /**
     * Handle the Business "updated" event.
     *
     * @param  \App\Models\Business  $business
     * @return void
     */
    public function updated(Business $business)
    {
        //
        // $this->updateUserProfilingPercentage($business);
    }

    /**
     * Handle the Business "deleted" event.
     *
     * @param  \App\Models\Business  $business
     * @return void
     */
    public function deleted(Business $business)
    {
        //
    }

    /**
     * Handle the Business "restored" event.
     *
     * @param  \App\Models\Business  $business
     * @return void
     */
    public function restored(Business $business)
    {
        //
    }

    /**
     * Handle the Business "force deleted" event.
     *
     * @param  \App\Models\Business  $business
     * @return void
     */
    public function forceDeleted(Business $business)
    {
        //
    }
}
