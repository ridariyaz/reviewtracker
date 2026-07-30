<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('tripadvisor_review_url', 2048)->nullable()->after('google_review_url');
            $table->string('yelp_review_url', 2048)->nullable()->after('tripadvisor_review_url');
            $table->string('trustpilot_review_url', 2048)->nullable()->after('yelp_review_url');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['tripadvisor_review_url', 'yelp_review_url', 'trustpilot_review_url']);
        });
    }
};
