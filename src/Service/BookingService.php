<?php

namespace App\Service;

use App\Repository\BookingRepository;

class BookingService
{
    // Déclaration de la variable pour le repository Booking
    private BookingRepository $bookingRepository;

    // Constructeur du service : Symfony injecte automatiquement le BookingRepository ici
    public function __construct(BookingRepository $bookingRepository)
    {
        // On assigne le repository à la variable pour pouvoir l'utiliser dans les méthodes
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Récupère toutes les réservations pour une date donnée.
     *
     * @param \DateTime $date La date des réservations à récupérer.
     * @return array Liste des réservations pour cette date.
     */
    public function getBookingsByDate(\DateTime $date): array
    {
        // Utilisation du repository pour récupérer les réservations en fonction de la date
        return $this->bookingRepository->findBy(['date' => $date]);
    }

    /**
     * Récupère les réservations en fonction d'un champ "exampleField" (exemple de recherche par un critère).
     *
     * @param mixed $value La valeur à rechercher dans le champ `exampleField`.
     * @return array Liste des réservations trouvées.
     */
    public function getBookingsByExampleField($value): array
    {
        // On utilise une méthode du repository pour rechercher des réservations avec un critère spécifique
        return $this->bookingRepository->findByExampleField($value);
    }

    /**
     * Récupère une réservation unique basée sur un champ spécifique "exampleField".
     *
     * @param mixed $value La valeur à rechercher.
     * @return ?Booking La réservation trouvée ou null si non trouvée.
     */
    public function getSingleBookingByExampleField($value): ?Booking
    {
        // Recherche d'une seule réservation en fonction d'un critère (exemple de champ "exampleField")
        return $this->bookingRepository->findOneBySomeField($value);
    }
}
