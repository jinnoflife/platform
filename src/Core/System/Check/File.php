<?php

declare(strict_types=1);

namespace Shopware\Core\System\Check;

use Symfony\Component\Finder\Finder;

class File
{
    private const HASH_PATH = __DIR__ . '/hashes.sha256';
    
    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir) { $this->projectDir = $projectDir; }

    public function checkFiles(): array
    {
        if(file_exists(self::HASH_PATH)) {
            $fileHashes = json_decode(file_get_contents(self::HASH_PATH), true) ?? [];

            foreach ($fileHashes as $filePath => $providedFileHash) {
                $fileAvailable = is_file($filePath);

                $isSame = false;
                if ($fileAvailable) {
                    $sha256Hash = hash('sha256', file_get_contents($filePath));
                    $isSame = ($sha256Hash === $providedFileHash);
                }
                
                $result[] = [
                    'name' => $filePath,
                    'available' => $fileAvailable,
                    'result' => $isSame,
                ];
            }
        }
        
        return $result;
    }
}
