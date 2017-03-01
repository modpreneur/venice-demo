<?php

namespace AppBundle\Services;

use GeneralBackend\CoreBundle\Entity\ProfilePhoto;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProfilePhotoUrlGenerator
 * @package AppBundle\Services
 */
class ProfilePhotoUrlGenerator
{
    /** @var CacheManager  */
    private $cacheManager;

    /** @var ContainerInterface  */
    private $serviceContainer;


    /**
     * ProfilePhotoUrlGenerator constructor.
     *
     * @param CacheManager $cacheManager
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(CacheManager $cacheManager, ContainerInterface $serviceContainer)
    {
        $this->cacheManager     = $cacheManager;
        $this->serviceContainer = $serviceContainer;
    }


    /**
     * @param ProfilePhoto $profilePhoto
     *
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function generateUrlToOriginalPhoto($profilePhoto)
    {
        $helper = $this->serviceContainer->get('vich_uploader.templating.helper.uploader_helper');

        $profilePhoto->getImageFile();

        return $this
                ->serviceContainer
                ->getParameter('amazon_base_path_with_bucket') .
            'profile_photos_original' . $helper->asset($profilePhoto, 'imageFile');
    }


    /**
     * @param ProfilePhoto $profilePhoto
     *
     * @return string
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function generateUrlToCroppedPhoto($profilePhoto)
    {
        try {
            $profilePhoto->getImageFile();

            $runtimeConfig = [
                'crop' => [
                    'start' => [
                        $profilePhoto->getCropStartX(),
                        $profilePhoto->getCropStartY()
                    ],
                    'size' => [
                        $profilePhoto->getCropSize(),
                        $profilePhoto->getCropSize()
                    ]
                ]
            ];

            $rcPath = $this->cacheManager->getRuntimePath($profilePhoto->getImageName(), $runtimeConfig);

            if (!$this->cacheManager->isStored($rcPath, 'profile_picture_cropped')) {
                $dataManager = $this->serviceContainer->get('liip_imagine.data.manager');
                $filter = $this->serviceContainer->get('liip_imagine.filter.manager');

                $dataManager->addLoader(
                    'profile_picture_cropped',
                    $this->serviceContainer->get('liip_imagine.binary.loader.stream.profile_photos')
                );

                $binary = $dataManager->find('profile_picture_cropped', $profilePhoto->getImageName());

                $this
                    ->cacheManager
                    ->store(
                        $filter
                            ->applyFilter(
                                $binary,
                                'profile_picture_cropped',
                                ['filters' => $runtimeConfig]
                            ),
                        $rcPath,
                        'profile_picture_cropped'
                    );
            }

            return $this
                ->serviceContainer
                ->get('venice.imagine.cache.resolver.proxy')
                ->resolve($rcPath, 'profile_picture_cropped');
        } catch (\Exception $exception) {
            $request = $this->serviceContainer->get('request_stack')->getCurrentRequest();
//            dump($exception);

            return $request->getUriForPath(
                'Resources/public/images/site/default-profile-photo.png'
            );
        }
    }
}