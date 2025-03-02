<?php
// src/Service/CommentaireFilterService.php

namespace App\Service;

class CommentaireFilterService
{
    // Liste noire de mots offensants
    private array $blacklist = [
        'hacker', 'badest', 'liarers', // Ajoute tes mots offensants ici
    ];

    public function filterContent(string $content): string
    {
        foreach ($this->blacklist as $badWord) {
            // Remplacer les mots offensants par des astérisques
            $content = str_ireplace($badWord, str_repeat('*', strlen($badWord)), $content);
        }

        return $content;
    }
}
