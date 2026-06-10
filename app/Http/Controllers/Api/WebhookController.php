<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WebhookSource;
use App\Jobs\ProcessWebhookEvent;

class WebhookController extends Controller
{
    public function handle(
        Request $request,
        string $project_uuid,
        string $source_key
    ) {
        $project = Project::withoutGlobalScopes()
            ->where('uuid', $project_uuid)
            ->first();

        if (!$project) {
            return response()->json([
                'message' => 'Project not found.'
            ], 404);
        }

        $source = WebhookSource::withoutGlobalScopes()
            ->where('project_id', $project->id)
            ->where('source_key', $source_key)
            ->where('is_active', true)
            ->first();

        if (!$source) {
            return response()->json([
                'message' => 'Webhook source not found.'
            ], 404);
        }

        if (
            $source->signing_secret &&
            !$this->verifySignature($request, $source)
        ) {
            return response()->json([
                'message' => 'Invalid signature.'
            ], 401);
        }

        ProcessWebhookEvent::dispatch(
            $source,
            $request->all()
        );

        return response()->json([
            'message' => 'Webhook accepted.'
        ], 202);
    }

    private function verifySignature(
        Request $request,
        WebhookSource $source
    ): bool {
        $signature = $request->header('X-Signature');

        if (!$signature) {
            return false;
        }

        $expected = hash_hmac(
            'sha256',
            $request->getContent(),
            $source->signing_secret
        );

        return hash_equals(
            $expected,
            $signature
        );
    }
}
