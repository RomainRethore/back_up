<?php

namespace App\Config;

enum ProductState: string
{
    case Good = 'Bon';
    case Mid = 'Moyen';
    case Bad = 'Mauvais';
}
