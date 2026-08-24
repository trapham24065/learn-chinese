<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TTSController extends Controller
{
    public function generate(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['error' => 'Text is required'], 400);
        }

        $region = env('AZURE_REGION', 'southeastasia');
        $key = env('AZURE_TTS_KEY');

        if (!$key) {
            return response()->json(['error' => 'Azure key not configured'], 500);
        }

        // Cache the audio file based on text hash to save quota!
        $cacheKey = 'tts_' . md5($text);
        
        $audioData = Cache::remember($cacheKey, now()->addDays(30), function () use ($text, $region, $key) {
            $url = "https://{$region}.tts.speech.microsoft.com/cognitiveservices/v1";
            
            $ssml = "<speak version='1.0' xml:lang='zh-CN'>
                        <voice xml:lang='zh-CN' xml:gender='Female' name='zh-CN-XiaoxiaoNeural'>
                            " . htmlspecialchars($text) . "
                        </voice>
                    </speak>";

            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $key,
                'Content-Type' => 'application/ssml+xml',
                'X-Microsoft-OutputFormat' => 'audio-16khz-128kbitrate-mono-mp3',
            ])->send('POST', $url, [
                'body' => $ssml
            ]);

            if ($response->successful()) {
                // Return base64 encoded string to save in cache
                return base64_encode($response->body());
            }

            return null;
        });

        if ($audioData) {
            return response()->json([
                'audio' => 'data:audio/mp3;base64,' . $audioData
            ]);
        }

        // Remove failed cache
        Cache::forget($cacheKey);
        
        return response()->json(['error' => 'TTS generation failed'], 500);
    }
}
