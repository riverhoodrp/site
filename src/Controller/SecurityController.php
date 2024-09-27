<?php

namespace App\Controller;

use Exception;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class SecurityController
{
    /**
     * @throws Exception
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new Exception('Logging out');
    }
}