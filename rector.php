<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessParamTagRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUselessReturnTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessReadOnlyTagRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withComposerBased(laravel: true)
    ->withCache(
        cacheDirectory: '/tmp/rector',
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths(paths: [
        __DIR__ . '/src',
    ])
    ->withPhpVersion(phpVersion: PhpVersion::PHP_83)
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        typeDeclarationDocblocks: true,
        privatization: true,
        earlyReturn: true,
        carbon: true,
    )
    ->withImportNames()
    ->withSkip([
        ChangeOrIfContinueToMultiContinueRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        EncapsedStringsToSprintfRector::class,
        RemoveUselessReadOnlyTagRector::class,
        RemoveUselessVarTagRector::class,
        RemoveUselessParamTagRector::class,
        RemoveUselessReturnTagRector::class,
        SeparateMultiUseImportsRector::class,
    ]);
