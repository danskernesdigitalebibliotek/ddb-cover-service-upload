<?php

/**
 * @file
 * Handle image storing at Cloudinary.
 */

namespace App\Service\CoverStore;

use App\Exception\CoverStoreCredentialException;
use App\Utils\CoverStore\CoverStoreItem;

/**
 * Class CloudinaryCoverStoreService.
 */
class CloudinaryCoverStoreService implements CoverStoreInterface
{
    private string $folder;

    /**
     * CloudinaryCoverStoreService constructor.
     *
     * @param string $bindCloudinaryCloudName
     *   The account cloud name
     * @param string $bindCloudinaryApiKey
     *   API key
     * @param string $bindCloudinaryApiSecret
     *   API secret
     *
     * @throws CoverStoreCredentialException
     */
    public function __construct(string $bindCloudinaryCloudName, string $bindCloudinaryApiKey, string $bindCloudinaryApiSecret, string $bindCloudinaryFolder)
    {
        if (empty($bindCloudinaryCloudName)) {
            throw new CoverStoreCredentialException('Missing Cloudinary configuration in environment: CLOUDINARY_CLOUD_NAME');
        }
        if (empty($bindCloudinaryApiKey)) {
            throw new CoverStoreCredentialException('Missing Cloudinary configuration in environment: CLOUDINARY_API_KEY');
        }
        if (empty($bindCloudinaryApiSecret)) {
            throw new CoverStoreCredentialException('Missing Cloudinary configuration in environment: CLOUDINARY_API_SECRET');
        }

        $this->folder = $bindCloudinaryFolder;

        // Set global Cloudinary configuration.
        \Cloudinary::config([
            'cloud_name' => $bindCloudinaryCloudName,
            'api_key' => $bindCloudinaryApiKey,
            'api_secret' => $bindCloudinaryApiSecret,
            'secure' => true, ]);
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $identifier = null): array
    {
        $search = new \Cloudinary\Search();
        $search
            ->expression('folder='.$this->folder)
            ->sort_by('public_id', 'desc')
            ->max_results(100);

        if (!is_null($identifier)) {
            $query = 'public_id:'.$this->folder.'/'.addcslashes($identifier, ':');
            $search->expression($query);
        }
        $result = $search->execute()->getArrayCopy();

        $items = [];
        foreach ($result['resources'] as $resources) {
            $item = new CoverStoreItem();
            $item->setId($resources['public_id'])
                ->setUrl($resources['secure_url'])
                ->setVendor($this->folder)
                ->setSize($resources['bytes'])
                ->setWidth((int) $resources['width'])
                ->setHeight((int) $resources['height'])
                ->setImageFormat($resources['format']);
            $items[] = $item;
        }

        return $items;
    }
}