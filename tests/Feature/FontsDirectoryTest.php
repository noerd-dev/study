<?php

declare(strict_types=1);

uses(Tests\TestCase::class);

it('ensures the storage fonts directory exists', function (): void {
    expect(is_dir(storage_path('fonts')))->toBeTrue();
});
