<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Google\Client as Google_Client;

class NotificationController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth');
    }

    public function sendNotification(Request $request)
    {
        if (Storage::disk('local')->has('firebase/credentials.json')) {
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/firebase/credentials.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $client_token = $client->getAccessToken();
            $access_token = $client_token['access_token'];
            $fcm_token = $request->fcm;
            if (!empty($access_token) && !empty($fcm_token)) {
                $projectId = env('FIREBASE_PROJECT_ID');
                $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
                foreach ($fcm_token as $fcm) {
                    $data = [
                        'message' => [
                            'notification' => [
                                'title' => $request->subject,
                                'body' => $request->message,
                            ],
                            'data' => [
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'id' => '1',
                                'status' => 'done',
                            ],
                            'token' => $fcm,
                        ],
                    ];
                    $headers = array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $access_token
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    $result = curl_exec($ch);
                    if ($result === FALSE) {
                        die('FCM Send Error: ' . curl_error($ch));
                    }
                    curl_close($ch);
                    $result = json_decode($result);
                    $response = array();
                    $response['success'] = true;
                    $response['message'] = 'Notification successfully sent.';
                    $response['result'] = $result;
                }
            } else {
                $response = array();
                $response['success'] = false;
                $response['message'] = 'Missing role or sender id or token to send notification.';
            }
        }
        $res = array('status' => true, 'response' => $response);
        echo json_encode($res);
    }

    /**
     * Booking push (distribution status change, turn approaching).
     * Sends the same title/body to every token, with data.type and data.distributionId so the
     * customer app can open the queue screen. A token that is UNREGISTERED or INVALID_ARGUMENT
     * is skipped and the others continue.
     */
    public function sendBookingNotification(Request $request)
    {
        $tokens = array_values(array_unique(array_filter((array) $request->input('fcm', []), function ($token) {
            return is_string($token) && trim($token) !== '';
        })));
        $title = (string) $request->input('title', '');
        $body = (string) $request->input('body', '');
        $type = (string) $request->input('type', '');
        $distributionId = (string) $request->input('distributionId', '');

        $result = ['status' => true, 'sent' => 0, 'invalid' => 0, 'failed' => 0];
        if (empty($tokens)) {
            return response()->json($result);
        }

        $accessToken = $this->getFcmAccessToken();
        if (empty($accessToken)) {
            return response()->json(['status' => false, 'message' => 'Missing Firebase credentials to send notification.']);
        }

        $url = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';

        foreach (array_chunk($tokens, 50) as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk, $url, $accessToken, $title, $body, $type, $distributionId) {
                $requests = [];
                foreach ($chunk as $token) {
                    $requests[] = $pool->withToken($accessToken)->timeout(20)->post($url, [
                        'message' => [
                            'token' => $token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => [
                                'type' => $type,
                                'distributionId' => $distributionId,
                            ],
                            'android' => [
                                'priority' => 'high',
                            ],
                        ],
                    ]);
                }
                return $requests;
            });

            foreach ($responses as $response) {
                if (!$response instanceof Response) {
                    $result['failed']++;
                } elseif ($response->successful()) {
                    $result['sent']++;
                } else {
                    $errorCode = $response->json('error.details.0.errorCode') ?? $response->json('error.status');
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        $result['invalid']++;
                    } else {
                        $result['failed']++;
                    }
                }
            }
        }

        return response()->json($result);
    }

    protected function getFcmAccessToken()
    {
        if (!Storage::disk('local')->has('firebase/credentials.json')) {
            return '';
        }
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/firebase/credentials.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $clientToken = $client->getAccessToken();

        return $clientToken['access_token'] ?? '';
    }
}
