<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_array', [$this, 'isArray']),
        ];
    }

    public function isArray($variable): bool
    {
        return is_array($variable);
    }
}