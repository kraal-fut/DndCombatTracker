<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Vite;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

beforeEach(function () {
    Vite::useBuildDirectory('build');
    Vite::useManifestFilename('manifest.json');
});
