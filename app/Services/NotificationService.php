<?php

// namespace App\Services;

// use App\Models\User;
// use App\Models\UserDevice;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Google\Client as GoogleClient;

// class NotificationService
// {
//     private $accessToken;
//     private $projectId;

//     public function __construct()
//     {
//         $this->initializeFirebase();
//     }

//     /**
//      * Initialize Firebase with direct service account authentication
//      */
//     private function initializeFirebase(): void
//     {
//         try {
//             $credentialsPath = storage_path('firebase/live-wallpapers-d9a58-firebase-adminsdk-fbsvc-e057aef797.json');

//             if (!file_exists($credentialsPath)) {
//                 throw new \Exception("Firebase service account file not found at: {$credentialsPath}");
//             }

//             $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 throw new \Exception("Invalid JSON in Firebase service account file");
//             }

//             $this->projectId = $serviceAccount['project_id'];

//             // Get access token using Google Auth
//             $client = new GoogleClient();
//             $client->setAuthConfig($credentialsPath);
//             $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
//             $client->fetchAccessTokenWithAssertion();

//             $accessTokenData = $client->getAccessToken();

//             if (!isset($accessTokenData['access_token'])) {
//                 throw new \Exception('Failed to obtain access token');
//             }

//             $this->accessToken = $accessTokenData['access_token'];

//             Log::info('Firebase initialized successfully', [
//                 'project_id' => $this->projectId,
//                 'token_expires' => $accessTokenData['expires_in'] ?? 'unknown'
//             ]);
//         } catch (\Exception $e) {
//             Log::error('Firebase initialization failed: ' . $e->getMessage());
//             throw $e;
//         }
//     }

//     /**
//      * Register a device token for push notifications
//      */
//     public function registerDevice(User $user, string $deviceToken, ?string $deviceType = null, ?string $platform = null, ?string $appVersion = null): UserDevice
//     {
//         UserDevice::where('device_token', $deviceToken)
//             ->update(['is_active' => false]);

//         return UserDevice::updateOrCreate(
//             [
//                 'user_id' => $user->id,
//                 'device_token' => $deviceToken,
//             ],
//             [
//                 'device_type' => $deviceType,
//                 'platform' => $platform,
//                 'app_version' => $appVersion,
//                 'is_active' => true,
//             ]
//         );
//     }

//     /**
//      * Unregister a device token
//      */
//     public function unregisterDevice(string $deviceToken): bool
//     {
//         return UserDevice::where('device_token', $deviceToken)
//             ->update(['is_active' => false]);
//     }

//     /**
//      * Send notification to a specific user
//      */
//     public function sendToUser(User $user, string $title, string $body, array $data = []): array
//     {
//         $deviceTokens = $user->devices()
//             ->where('is_active', true)
//             ->pluck('device_token')
//             ->toArray();

//         if (empty($deviceTokens)) {
//             return ['success' => false, 'message' => 'No active devices found for user'];
//         }

//         return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
//     }

//     /**
//      * Send notification to multiple devices using direct HTTP API
//      */
//     public function sendToMultipleDevices(array $deviceTokens, string $title, string $body, array $data = []): array
//     {
//         if (empty($deviceTokens)) {
//             return ['success' => false, 'message' => 'No device tokens provided'];
//         }

//         // Filter out empty tokens
//         $deviceTokens = array_filter($deviceTokens);
//         $deviceTokens = array_values($deviceTokens); // Reindex array

//         if (empty($deviceTokens)) {
//             return ['success' => false, 'message' => 'No valid device tokens provided'];
//         }

//         $results = [
//             'success' => true,
//             'sent_count' => 0,
//             'failure_count' => 0,
//             'successes' => [],
//             'failures' => [],
//             'total_tokens' => count($deviceTokens)
//         ];

//         Log::info('Sending notifications via HTTP API', [
//             'token_count' => count($deviceTokens),
//             'title' => $title,
//             'project_id' => $this->projectId
//         ]);

//         // Send to each token individually (more reliable than batch)
//         foreach ($deviceTokens as $token) {
//             $result = $this->sendSingleNotification($token, $title, $body, $data);

//             if ($result['success']) {
//                 $results['sent_count']++;
//                 $results['successes'][] = [
//                     'device_token' => $token,
//                     'message_id' => $result['message_id']
//                 ];
//             } else {
//                 $results['failure_count']++;
//                 $results['failures'][] = [
//                     'device_token' => $token,
//                     'error' => $result['error']
//                 ];

//                 // Deactivate invalid tokens
//                 if ($this->isTokenInvalid($result['error'])) {
//                     $this->unregisterDevice($token);
//                 }
//             }
//         }

//         Log::info('Notification delivery completed', [
//             'sent_count' => $results['sent_count'],
//             'failure_count' => $results['failure_count']
//         ]);

//         return $results;
//     }

//     /**
//      * Send single notification to one device token
//      */
//     private function sendSingleNotification(string $deviceToken, string $title, string $body, array $data = []): array
//     {
//         try {
//             $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

//             $payload = [
//                 'message' => [
//                     'token' => $deviceToken,
//                     'notification' => [
//                         'title' => $title,
//                         'body' => $body,
//                         'image' => $data['image'] ?? null,
//                         // 'media_type' => $data['media_type'] ?? null,
//                         // 'parent_id' => $data['parent_id'] ?? null,
//                     ],
//                     'data' => array_merge($data, [
//                         'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
//                         "media-url" => $data['image'] ?? ''
//                     ]),
//                     'android' => [
//                         'priority' => 'high',
//                         'notification' => [
//                             'image' => $data['image'] ?? null, // <-- Android-specific image support
//                         ]
//                     ],
//                     'apns' => [
//                         'payload' => [
//                             'aps' => [
//                                 'content-available' => 1,
//                                 'alert' => [
//                                     'title' => $title,
//                                     'body' => $body,
//                                 ],
//                                 'sound' => 'default',
//                             ],
//                         ],
//                         'headers' => [
//                             'apns-priority' => '10',
//                         ],
//                     ],
//                     'webpush' => [
//                         'headers' => [
//                             'Urgency' => 'high',
//                         ],
//                     ],
//                 ]
//             ];

//             $response = Http::withHeaders([
//                 'Authorization' => 'Bearer ' . $this->accessToken,
//                 'Content-Type' => 'application/json',
//             ])
//                 ->timeout(30)
//                 ->post($url, $payload);

//             if ($response->successful()) {
//                 $responseData = $response->json();
//                 return [
//                     'success' => true,
//                     'message_id' => $responseData['name'] ?? 'unknown'
//                 ];
//             } else {
//                 $errorMessage = $response->body();
//                 $statusCode = $response->status();

//                 Log::warning('FCM API error', [
//                     'token' => substr($deviceToken, 0, 10) . '...', // Log partial token for security
//                     'status' => $statusCode,
//                     'error' => $errorMessage
//                 ]);

//                 return [
//                     'success' => false,
//                     'error' => "HTTP {$statusCode}: {$errorMessage}"
//                 ];
//             }
//         } catch (\Exception $e) {
//             Log::error('Single notification failed', [
//                 'token' => substr($deviceToken, 0, 10) . '...',
//                 'error' => $e->getMessage()
//             ]);

//             return [
//                 'success' => false,
//                 'error' => $e->getMessage()
//             ];
//         }
//     }

//     /**
//      * Send notification to all active users
//      */
//     public function sendToAllUsers(string $title, string $body, array $data = []): array
//     {
//         $deviceTokens = UserDevice::where('is_active', true)
//             ->pluck('device_token')
//             ->toArray();

//         return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
//     }

//     /**
//      * Check if token is invalid based on error message
//      */
//     private function isTokenInvalid(string $error): bool
//     {
//         $invalidErrors = [
//             'registration-token-not-registered',
//             'invalid-registration-token',
//             'mismatch-sender-id',
//             'not-registered',
//             'unregistered',
//             'invalid_argument'
//         ];

//         $errorLower = strtolower($error);

//         foreach ($invalidErrors as $invalidError) {
//             if (strpos($errorLower, $invalidError) !== false) {
//                 return true;
//             }
//         }

//         return false;
//     }

//     /**
//      * Test Firebase connection (for debugging)
//      */
//     public function testConnection(): array
//     {
//         try {
//             $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}";

//             $response = Http::withHeaders([
//                 'Authorization' => 'Bearer ' . $this->accessToken,
//                 'Content-Type' => 'application/json',
//             ])->get($url);

//             return [
//                 'success' => $response->successful(),
//                 'status' => $response->status(),
//                 'project_id' => $this->projectId,
//                 'response' => $response->successful() ? 'Connected successfully' : $response->body()
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'success' => false,
//                 'error' => $e->getMessage(),
//                 'project_id' => $this->projectId
//             ];
//         }
//     }
// }





namespace App\Services;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;

class NotificationService
{
    private $accessToken;
    private $projectId;

    // The universal topic name for broadcasting to all users
    const ALL_USERS_TOPIC = 'all_users';

    public function __construct()
    {
        $this->initializeFirebase();
    }

    /**
     * Initialize Firebase with direct service account authentication
     */
    private function initializeFirebase(): void
    {
        try {
            // NOTE: Ensure your service account file path is correct
            $credentialsPath = storage_path('firebase/live-wallpapers-d9a58-firebase-adminsdk-fbsvc-e057aef797.json');

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase service account file not found at: {$credentialsPath}");
            }

            $serviceAccount = json_decode(file_get_contents($credentialsPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON in Firebase service account file");
            }

            $this->projectId = $serviceAccount['project_id'];

            // Get access token using Google Auth
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();

            $accessTokenData = $client->getAccessToken();

            if (!isset($accessTokenData['access_token'])) {
                throw new \Exception('Failed to obtain access token');
            }

            $this->accessToken = $accessTokenData['access_token'];

            Log::info('Firebase initialized successfully', [
                'project_id' => $this->projectId
            ]);
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // --- Topic Management ---

    /**
     * Subscribe a device token to an FCM topic.
     * Uses the legacy IID API, which typically requires the legacy Server Key.
     * Ensure your config has the FCM_SERVER_KEY set correctly.
     */
    private function subscribeToTopic(string $deviceToken, string $topicName): bool
    {
        // NOTE: The IID API for topic management often requires the legacy Server Key (not the access token)
        $serverKey = env('FCM_SERVER_KEY');

        if (empty($serverKey)) {
            Log::error('FCM_SERVER_KEY is not set for topic subscription.');
            return false;
        }

        try {
            $url = "https://iid.googleapis.com/iid/v1:batchAdd";

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, [
                    'to' => "/topics/{$topicName}",
                    'registration_tokens' => [$deviceToken]
                ]);

            if ($response->successful()) {
                Log::info("Token subscribed to topic {$topicName}", ['token' => substr($deviceToken, 0, 10) . '...']);
                return true;
            } else {
                Log::warning('Failed to subscribe token to topic', ['token' => substr($deviceToken, 0, 10) . '...', 'topic' => $topicName, 'response' => $response->body()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception during topic subscription', ['error' => $e->getMessage()]);
            return false;
        }
    }


    // --- Device Management ---

    /**
     * Register a device token for push notifications AND subscribe it to the ALL_USERS_TOPIC.
     */
    public function registerDevice(User $user, string $deviceToken, ?string $deviceType = null, ?string $platform = null, ?string $appVersion = null): UserDevice
    {
        // Deactivate any existing tokens for this device
        UserDevice::where('device_token', $deviceToken)
            ->update(['is_active' => false]);

        // Create or update the device token
        $userDevice = UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_token' => $deviceToken,
            ],
            [
                'device_type' => $deviceType,
                'platform' => $platform,
                'app_version' => $appVersion,
                'is_active' => true,
            ]
        );

        // NEW STEP: Automatically subscribe this new token to the universal broadcast topic
        $this->subscribeToTopic($deviceToken, self::ALL_USERS_TOPIC);

        return $userDevice;
    }

    /**
     * Unregister a device token
     */
    public function unregisterDevice(string $deviceToken): bool
    {
        return UserDevice::where('device_token', $deviceToken)
            ->update(['is_active' => false]);
    }


    // --- Sending Notifications ---

    /**
     * Send notification to a specific user (still requires token lookup)
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $deviceTokens = $user->devices()
            ->where('is_active', true)
            ->pluck('device_token')
            ->toArray();

        if (empty($deviceTokens)) {
            return ['success' => false, 'message' => 'No active devices found for user'];
        }

        // Use the token-based send method for targeted messages
        return $this->sendToMultipleDevices($deviceTokens, $title, $body, $data);
    }

    /**
     * Send single notification to one device token (used by sendToUser and sendToMultipleDevices)
     * (Retained from original code for targeted sends)
     */
    private function sendSingleNotification(string $deviceToken, string $title, string $body, array $data = []): array
    {
        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => $data['image'] ?? null,
                    ],
                    'data' => array_merge($data, [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        "media-url" => $data['image'] ?? ''
                    ]),
                    // Platform-specific configurations...
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'image' => $data['image'] ?? null,
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                                'alert' => ['title' => $title, 'body' => $body],
                                'sound' => 'default',
                            ],
                        ],
                        'headers' => ['apns-priority' => '10'],
                    ],
                    // 'webpush' => [...],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, $payload);

            // ... (success/failure handling as per your original code) ...
            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'success' => true,
                    'message_id' => $responseData['name'] ?? 'unknown'
                ];
            } else {
                $errorMessage = $response->body();
                $statusCode = $response->status();

                Log::warning('FCM API error', [
                    'token' => substr($deviceToken, 0, 10) . '...',
                    'status' => $statusCode,
                    'error' => $errorMessage
                ]);

                return [
                    'success' => false,
                    'error' => "HTTP {$statusCode}: {$errorMessage}"
                ];
            }
        } catch (\Exception $e) {
            Log::error('Single notification failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send notification to multiple devices by iterating over tokens.
     * (Retained for targeted sends to multiple devices, but not used for broadcast anymore)
     */
    public function sendToMultipleDevices(array $deviceTokens, string $title, string $body, array $data = []): array
    {
        // Token filtering and iteration logic remains here for targeted sends
        $deviceTokens = array_filter($deviceTokens);
        if (empty($deviceTokens)) {
            return ['success' => false, 'message' => 'No valid device tokens provided'];
        }

        $results = [
            'success' => true,
            'sent_count' => 0,
            'failure_count' => 0,
            'successes' => [],
            'failures' => [],
            'total_tokens' => count($deviceTokens)
        ];

        foreach ($deviceTokens as $token) {
            $result = $this->sendSingleNotification($token, $title, $body, $data);

            if ($result['success']) {
                $results['sent_count']++;
                $results['successes'][] = ['device_token' => $token, 'message_id' => $result['message_id']];
            } else {
                $results['failure_count']++;
                $results['failures'][] = ['device_token' => $token, 'error' => $result['error']];

                if ($this->isTokenInvalid($result['error'])) {
                    $this->unregisterDevice($token);
                }
            }
        }
        return $results;
    }

    /**
     * Send notification to an FCM topic. This is the new, efficient broadcast method.
     */
    public function sendToTopic(string $topicName, string $title, string $body, array $data = []): array
    {
        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $payload = [
                'message' => [
                    'topic' => $topicName, // <-- Key Change: Targeting the topic name
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => $data['image'] ?? null,
                    ],
                    'data' => array_merge($data, [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        "media-url" => $data['image'] ?? ''
                    ]),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'image' => $data['image'] ?? null,
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                                'alert' => ['title' => $title, 'body' => $body],
                                'sound' => 'default',
                            ],
                        ],
                        'headers' => ['apns-priority' => '10'],
                    ],
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($url, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info("Topic message sent successfully", ['topic' => $topicName, 'message_id' => $responseData['name'] ?? 'unknown']);
                return [
                    'success' => true,
                    'message' => 'Notification successfully broadcast to topic.',
                    'message_id' => $responseData['name'] ?? 'unknown'
                ];
            } else {
                $errorMessage = $response->body();
                $statusCode = $response->status();
                Log::error('FCM Topic API error', ['status' => $statusCode, 'error' => $errorMessage]);
                return [
                    'success' => false,
                    'message' => "FCM Topic API error: {$statusCode} - {$errorMessage}"
                ];
            }
        } catch (\Exception $e) {
            Log::error('Topic notification failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Topic notification failed: ' . $e->getMessage()];
        }
    }

    /**
     * Send notification to all active users using the Topic Broadcast method.
     */
    public function sendToAllUsers(string $title, string $body, array $data = []): array
    {
        // This function no longer queries the database for tokens!
        // It simply targets the universal topic.
        return $this->sendToTopic(self::ALL_USERS_TOPIC, $title, $body, $data);
    }

    // --- Utility Methods ---

    /**
     * Check if token is invalid based on error message
     */
    private function isTokenInvalid(string $error): bool
    {
        $invalidErrors = [
            'registration-token-not-registered',
            'invalid-registration-token',
            'mismatch-sender-id',
            'not-registered',
            'unregistered',
            'invalid_argument'
        ];

        $errorLower = strtolower($error);

        foreach ($invalidErrors as $invalidError) {
            if (strpos($errorLower, $invalidError) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Test Firebase connection (for debugging)
     */
    public function testConnection(): array
    {
        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])->get($url);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'project_id' => $this->projectId,
                'response' => $response->successful() ? 'Connected successfully' : $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'project_id' => $this->projectId
            ];
        }
    }
}
