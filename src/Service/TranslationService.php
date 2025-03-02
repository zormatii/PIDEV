<?php
namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TranslationService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function translate(string $text, string $sourceLang, string $targetLang): string
    {

         // Vérifie si la langue source et la langue cible sont identiques
         if ($sourceLang === $targetLang) {
            return $text;
        }

        // Vérification pour détecter la vraie langue source
    if ($sourceLang === 'auto') {
        $sourceLang = $this->detectLanguage($text);
    }




        $url = sprintf(
            'https://api.mymemory.translated.net/get?q=%s&langpair=%s|%s',
            urlencode($text),
            $sourceLang,
            $targetLang
        );

        try {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();
    
            return $data['responseData']['translatedText'] ?? 'Erreur de traduction';
        } catch (\Exception $e) {
            return 'Erreur de connexion à l’API';
        }
    }
    
    // Détection simple de la langue du texte
    private function detectLanguage(string $text): string
    {
        return preg_match('/[éèàùçêîôû]/', $text) ? 'fr' : 'en';
    }

}