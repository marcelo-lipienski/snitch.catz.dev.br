<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class RepositoryController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'url' => ['required', 'string', 'url'],
        ]);

        $url = $request->input('url');
        $tempDir = storage_path('app/temp_repos/' . Str::random(32));

        try {
            // 1. Verify if it's a valid git repository
            // Use array for Process::run to avoid shell injection and disable terminal prompts
            $lsRemote = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run(['git', 'ls-remote', '--exit-code', $url]);

            if (!$lsRemote->successful()) {
                return response()->json(['valid' => false, 'error' => 'Invalid git repository'], 422);
            }

            // 2. Check for PHP files
            // Using a bare clone with blob:none filter to minimize data transfer
            $clone = Process::env(['GIT_TERMINAL_PROMPT' => '0'])
                ->run(['git', 'clone', '--depth', '1', '--bare', '--filter=blob:none', $url, $tempDir]);

            if (!$clone->successful()) {
                return response()->json(['valid' => false, 'error' => 'Failed to clone repository metadata'], 422);
            }

            $lsTree = Process::run(['git', '--git-dir=' . $tempDir, 'ls-tree', '-r', 'HEAD', '--name-only']);
            $files = explode("\n", $lsTree->output());
            
            $hasPhpFile = false;
            foreach ($files as $file) {
                if (Str::endsWith(trim($file), '.php')) {
                    $hasPhpFile = true;
                    break;
                }
            }

            if (!$hasPhpFile) {
                return response()->json(['valid' => false, 'error' => 'No PHP files found'], 422);
            }

            return response()->json(['valid' => true]);

        } finally {
            // Cleanup
            if (file_exists($tempDir)) {
                Process::run(['rm', '-rf', $tempDir]);
            }
        }
    }
}
