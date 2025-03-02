<?php 
namespace App\Normalizer;

use App\Entity\Evenement;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class EvenementNormalizer implements ContextAwareNormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            'titre' => $object->getTitre(),
            'description' => $object->getDescription(),
            'lieu' => $object->getLieu(),
            'nombreDePlaces' => $object->getNombreDePlaces(),
            'dateDebut' => $object->getDateDebut()?->format('Y-m-d H:i:s'),
            'dateFin' => $object->getDateFin()?->format('Y-m-d H:i:s'),
            'urlImage' => $object->getUrlImage(),
            'statut' => $object->getStatut(),
            'categorie' => $object->getCategorie() ? ['nom' => $object->getCategorie()->getNom()] : null,
        ];
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        return $data instanceof Evenement;
    }
}
