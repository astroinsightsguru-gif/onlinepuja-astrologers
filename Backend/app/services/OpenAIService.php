<?php

namespace App\Services;

use App\Models\AdminModel\SystemFlag;
use GuzzleHttp\Client;
use Exception;
use App\Models\AiAstrologerModel\AiAstrologer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OpenAIService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $api_key=SystemFlag::where('name','OpenAiKey')->first();
        $this->apiKey = $api_key->value;
        if (!$this->apiKey) {
            \Log::error('OpenAI API Key not found');
            throw new Exception('API Key not found.');
        }
    }

    public function askChatGPT($message,$astrologerId)
    {

        $assistantContent = $this->getAssistantContentBasedOnAstrologer($astrologerId); // Example function to get dynamic content based on astrologer ID
         $userId = authcheck()['id'];
        $user = User::where('id', $userId)->select(['name', 'birthDate', 'birthPlace'])->first();
        
        // Format the user data into the message
        $userInfo = "mera name {$user->name}, mera date of birth {$user->birthDate}, aur mera place of birth is {$user->birthPlace}.";
        $finalMessage = "{$userInfo} {$message}";

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [

                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => $assistantContent],
                        ['role' => 'user', 'content' => $finalMessage],
                    ],
                    'max_tokens' => 200,
                    'temperature' => 0.5,
                    'top_p' => 0.7,
                    'frequency_penalty'=>0,
                    'presence_penalty'=>0,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $content = $data['choices'][0]['message']['content'];
            $content = $this->stopAtLastPeriod($content);

            return $content;
        } catch (RequestException $e) {
            if ($e->getCode() == 429) {
                $attempts++;
                sleep(2);
            } else {
                \Log::error('Request error: ' . $e->getMessage());
                return 'Error communicating with OpenAI API.';
            }
        } catch (Exception $e) {
            \Log::error('General error: ' . $e->getMessage());
            return $e->getMessage();
        }

    }

    public function askChatGPTMaster($message)
    {
        $assistantContent = AiAstrologer::where('type','master')->value('system_intruction');
        $userId = authcheck()['id'];
        $user = User::where('id', $userId)->select(['name', 'birthDate', 'birthPlace'])->first();
        
        // $userInfo = "My name is {$user->name}, my date of birth is {$user->birthDate}, and my place of birth is {$user->birthPlace}.";
        $userInfo = "mera name {$user->name}, mera date of birth {$user->birthDate}, aur mera place of birth is {$user->birthPlace}.";

        $finalMessage = "{$userInfo} {$message}";

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [

                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4',
                    'messages' => [
                        ['role' => 'system', 'content' => $assistantContent],
                        ['role' => 'user', 'content' => $finalMessage],
                    ],
                    'max_tokens' => 350,
                    'temperature' => 0.5,
                    'top_p' => 0.7,
                    'frequency_penalty'=>0,
                    'presence_penalty'=>0,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $content = $data['choices'][0]['message']['content'];
            $content = $this->stopAtLastPeriod($content);

            return $content;
        } catch (RequestException $e) {
            if ($e->getCode() == 429) {
                $attempts++;
                sleep(2);
            } else {
                \Log::error('Request error: ' . $e->getMessage());
                return 'Error communicating with OpenAI API.';
            }
        } catch (Exception $e) {
            \Log::error('General error: ' . $e->getMessage());
            return $e->getMessage();
        }

    }

    private function stopAtLastPeriod($content)
    {
        // Trim any extra spaces from the response
        $content = trim($content);

        // Find the position of the last period (.)
        $lastPeriodPos = strrpos($content, '.');

        // If a period is found, truncate everything after the last period
        if ($lastPeriodPos !== false) {
            // Cut the response to the position of the last period
            $content = substr($content, 0, $lastPeriodPos + 1);
        }

        // Return the trimmed content
        return $content;
    }
    private function getAssistantContentBasedOnAstrologer($astrologerId)
    {
        $astrologer = AiAstrologer::find($astrologerId);

        if ($astrologer) {
            return $astrologer->system_intruction;
        }
        return "You are an experienced astrologer. Your role is to provide insightful and personalized
          astrological readings based on users' birth details. Use your knowledge of astrology to interpret planetary
           positions, aspects, and transits to help users understand their past, present, and future. Be empathetic
            and supportive in your responses.";
    }



}
