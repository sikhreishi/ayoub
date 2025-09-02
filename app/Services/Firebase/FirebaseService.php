<?php
namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\Contract\Messaging;  // Change to Contract\Messaging
use Kreait\Firebase\Messaging\CloudMessage;
use App\Services\Location\GeohashService;
use Illuminate\Support\Facades\Log;


class FirebaseService
{
    protected Database $database;
    protected Messaging $messaging;
    protected GeohashService $geohashService;

    public function __construct(GeohashService $geohashService)
    {
        $this->geohashService = $geohashService;

        // Initialize Firebase

        $factory = (new Factory)
            ->withServiceAccount( base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $factory->createDatabase();
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Initialize a driver record in Firebase
     */
    public function initializeDriverRecord(string $driverId): void
    {
        $geohash = $this->geohashService->encode(1, 1); // Or any actual default coordinates

        try {
            $this->database
                ->getReference('drivers/' . $driverId)
                ->set([
                    'lat' => 1,
                    'long' => 1,
                    'geohash' => $geohash,
                    'created_at' => time(),
                    'updated_at' => time()
                ]);
        } catch (\Exception $e) {
            Log::error("Error initializing driver record for driver {$driverId}: " . $e->getMessage());
        }
    }

    public function deleteDriverRecord(string $driverId): void
    {
        try {
            $this->database
                ->getReference('drivers/' . $driverId)
                ->remove();  // Delete the driver's record from Firebase
        } catch (\Exception $e) {
            Log::error("Error deleting driver record for driver {$driverId}: " . $e->getMessage());
        }
    }

    /**
     * Update a driver's location in Firebase
     */
    public function updateDriverLocation(string $driverId, ?float $lat, ?float $long, string $geohash): void
    {
        try {
            $this->database
                ->getReference('drivers/' . $driverId)
                ->update([
                    'lat' => $lat,
                    'long' => $long,
                    'geohash' => $geohash,
                    'updated_at' => time()
                ]);
        } catch (\Exception $e) {
            Log::error("Error updating driver location for driver {$driverId}: " . $e->getMessage());
        }
    }


    public function getDriversByGeohashPrefix(string $userGeohash)
    {
        try {
            $geohashPrefix = substr($userGeohash, 0, 6);

            // Query Firebase database to get drivers by geohash prefix
            $drivers = $this->database->getReference('drivers')
                ->orderByChild('geohash')
                ->startAt($geohashPrefix)
                ->endAt($geohashPrefix . '~')
                ->getValue();
            Log::info("Found " . count($drivers) . " available drivers for geohash: " . $userGeohash);
            return $drivers;
        } catch (\Exception $e) {
            Log::error("Error fetching drivers by geohash: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Send Firebase Cloud Message (FCM) notification
     */
    public function sendNotification(string $firebaseToken, string $title, string $body, array $data)
    {
        try {
            $message = CloudMessage::new()
                ->withTarget('token', $firebaseToken) // Target the driver's token
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ])
                ->withData($data);

            // Send the message
            $this->messaging->send($message);

            Log::info("Notification sent successfully to driver with token: " . $firebaseToken);
        } catch (\Exception $e) {
            Log::error("Error sending Firebase Cloud Message: " . $e->getMessage());
        }
    }

    /**
     * Fetch a driver by their ID from Firebase
     */
    public function getDriverById(string $driverId)
    {
        try {
            $driver = $this->database
                ->getReference('drivers/' . $driverId)
                ->getValue();

            return $driver ?: null;
        } catch (\Exception $e) {
            Log::error("Error fetching driver data for driver {$driverId}: " . $e->getMessage());
            return null;
        }
    }


    public function storeTripInFirebase($tripId, $status)
    {
        try {
            // Reference the 'trips' node and store the trip data by trip ID
            $tripRef = $this->database->getReference('trips/' . $tripId);

            // Set the status and trip_id
            $tripRef->set([
                'status' => $status,  // Status of the trip (e.g., 'pending', 'accepted', etc.)
            ]);

            Log::info("Trip data (ID: $tripId) stored in Firebase with status: $status");
        } catch (\Exception $e) {
            Log::error("Error storing trip data in Firebase: " . $e->getMessage());
        }
    }

    /**
     * Get driver location from Firebase by driver ID
     */
    public function getDriverLocationFromFirebase(string $driverId)
    {
        try {
            $driver = $this->database
                ->getReference('drivers/' . $driverId)
                ->getValue();

            if ($driver) {
                return [
                    'lat' => $driver['lat'] ?? null,
                    'lng' => $driver['long'] ?? null,
                    'timestamp' => $driver['updated_at'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error fetching driver location from Firebase for driver {$driverId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Store ticket reply in Firebase for real-time updates
     */
    public function storeTicketReply(int $ticketId, array $replyData): void
    {
        try {
            // Add timestamp for proper ordering
            $replyData['timestamp'] = time();
            $replyData['server_timestamp'] = ['.sv' => 'timestamp'];

            $this->database
                ->getReference('tickets/' . $ticketId . '/replies')
                ->push($replyData);

            Log::info("Ticket reply stored in Firebase for ticket: {$ticketId}");
        } catch (\Exception $e) {
            Log::error("Error storing ticket reply in Firebase: " . $e->getMessage());
        }
    }

    /**
     * Get ticket replies from Firebase
     */
    public function getTicketReplies(int $ticketId)
    {
        try {
            $replies = $this->database
                ->getReference('tickets/' . $ticketId . '/replies')
                ->getValue();

            return $replies ?: [];
        } catch (\Exception $e) {
            Log::error("Error fetching ticket replies from Firebase: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Store ticket status update in Firebase
     */
    public function updateTicketStatus(int $ticketId, string $status, string $updatedBy): void
    {
        try {
            $this->database
                ->getReference('tickets/' . $ticketId . '/status')
                ->set([
                    'status' => $status,
                    'updated_by' => $updatedBy,
                    'updated_at' => time()
                ]);

            Log::info("Ticket status updated in Firebase for ticket: {$ticketId}");
        } catch (\Exception $e) {
            Log::error("Error updating ticket status in Firebase: " . $e->getMessage());
        }
    }

    /**
     * Store ticket priority update in Firebase
     */
    public function updateTicketPriority(int $ticketId, string $priority, string $updatedBy): void
    {
        try {
            $this->database
                ->getReference('tickets/' . $ticketId . '/priority')
                ->set([
                    'priority' => $priority,
                    'updated_by' => $updatedBy,
                    'updated_at' => time()
                ]);

            Log::info("Ticket priority updated in Firebase for ticket: {$ticketId}");
        } catch (\Exception $e) {
            Log::error("Error updating ticket priority in Firebase: " . $e->getMessage());
        }
    }
}





