<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;


#[Route('/api/auth')]
final class VkAuthController extends AbstractController
{
    #[Route('/vk/callback', name: 'api_auth_vk_callback', methods: ['GET'])]
    public function callback(
        Request $request,
        ClientRegistry $clientRegistry,
    ) {
        // $client = $clientRegistry->getClient('vk');

        // $vkUser = $client->fetchUser();

       return $clientRegistry
            // Имя клиента, указанное в config/packages/knpu_oauth2_client.yaml
            ->getClient('vkontakte_client')
            // scopes (см доку ВКонтакта, мне нужны эти)
            ->redirect(['public_profile', 'email', 'groups']);
    }

}
