<?php

namespace App\Controller;

use App\Service\BookingService;
use App\Entity\Booking;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/booking', name: 'app_api_booking_')]
class BookingController extends AbstractController
{
    private BookingService $bookingService;
    private SerializerInterface $serializer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        BookingService $bookingService,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->bookingService = $bookingService;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route(methods: 'POST')]
    /** @OA\Post(
     *     path="/api/booking",
     *     summary="Créer une réservation",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la réservation à créer",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2025-01-26T15:00:00"),
     *             @OA\Property(property="numOfPeople", type="integer", example=4)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Réservation créée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2025-01-26T15:00:00"),
     *             @OA\Property(property="numOfPeople", type="integer", example=4),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $booking = $this->bookingService->createBooking($data);

        $responseData = $this->serializer->serialize($booking, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_booking_show',
            ['id' => $booking->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /** @OA\Get(
     *     path="/api/booking/{id}",
     *     summary="Afficher une réservation par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à afficher",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Réservation trouvée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2025-01-26T15:00:00"),
     *             @OA\Property(property="numOfPeople", type="integer", example=4),
     *             @OA\Property(property="createdAt", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingService->findBookingById($id);
        if ($booking) {
            $responseData = $this->serializer->serialize($booking, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /** @OA\Put(
     *     path="/api/booking/{id}",
     *     summary="Modifier une réservation par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à modifier",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Nouvelles données de la réservation à mettre à jour",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2025-01-26T16:00:00"),
     *             @OA\Property(property="numOfPeople", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Réservation modifiée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $booking = $this->bookingService->findBookingById($id);
        if ($booking) {
            $this->bookingService->updateBooking($booking, $data);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /** @OA\Delete(
     *     path="/api/booking/{id}",
     *     summary="Supprimer une réservation par ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la réservation à supprimer",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Réservation supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Réservation non trouvée"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $booking = $this->bookingService->findBookingById($id);
        if ($booking) {
            $this->bookingService->deleteBooking($booking);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }




    /**
     * @Route("/api/bookings/{date}", name="get_bookings_by_date", methods="GET")
     */
    public function getBookingsByDate(string $date): JsonResponse
    {
        // Convertir la chaîne de caractères en objet DateTime
        $dateObj = new \DateTime($date);

        // Utilisation du service pour récupérer les réservations pour cette date
        $bookings = $this->bookingService->getBookingsByDate($dateObj);

        if (!empty($bookings)) {
            return new JsonResponse($bookings, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Aucune réservation trouvée pour cette date.'], Response::HTTP_NOT_FOUND);
    }

}
