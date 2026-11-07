<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StitchService
{
    /** Base URL for the Antigravity MCP gateway */
    private string $gateway = 'http://localhost:8000/mcp';

    /** Generic call to any Stitch MCP tool */
    public function callMcp(string $tool, array $payload = []): array
    {
        $body = [
            'server'    => 'stitch',
            'tool'      => $tool,
            'arguments' => $payload,
        ];

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('STITCH_API_TOKEN'),
            ])
            ->post($this->gateway, $body);

        $response->throw();
        return $response->json();
    }

    /** Create a new Stitch project */
    public function createProject(?string $title = null): array
    {
        $payload = [];
        if ($title !== null) {
            $payload['title'] = $title;
        }
        return $this->callMcp('create_project', $payload);
    }

    /** List all Stitch projects */
    public function listProjects(): array
    {
        return $this->callMcp('list_projects');
    }

    /** Upload a design markdown file */
    public function uploadDesign(string $projectId, string $designMd): array
    {
        return $this->callMcp('upload_design_md', [
            'project_id' => $projectId,
            'design_md'  => $designMd,
        ]);
    }

    /** Generate a screen from a textual prompt */
    public function generateScreen(string $projectId, string $prompt): array
    {
        return $this->callMcp('generate_screen_from_text', [
            'project_id' => $projectId,
            'prompt'     => $prompt,
        ]);
    }

    /** Download a generated file (e.g. code bundle) */
    public function downloadFile(string $projectId, string $path): array
    {
        return $this->callMcp('download_file', [
            'project_id' => $projectId,
            'path'       => $path,
        ]);
    }
}
