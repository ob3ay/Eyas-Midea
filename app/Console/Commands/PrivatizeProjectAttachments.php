<?php

namespace App\Console\Commands;

use App\Models\ProjectAttachment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('attachments:privatize-projects')]
#[Description('Move legacy project attachments from the public disk to private storage.')]
class PrivatizeProjectAttachments extends Command
{
    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('local');
        $moved = 0;
        $failed = 0;

        ProjectAttachment::query()
            ->where('disk', 'public')
            ->orderBy('id')
            ->chunkById(100, function ($attachments) use ($public, $private, &$moved, &$failed): void {
                foreach ($attachments as $attachment) {
                    $path = ltrim((string) $attachment->file_path, '/');
                    if (! str_starts_with($path, 'projects/attachments/')) {
                        $this->components->error("Skipped attachment {$attachment->id}: unexpected storage path.");
                        $failed++;
                        continue;
                    }

                    if (! $private->exists($path)) {
                        if (! $public->exists($path) || ! $private->put($path, $public->get($path))) {
                            $this->components->error("Could not copy attachment {$attachment->id}.");
                            $failed++;
                            continue;
                        }
                    }

                    if ($public->exists($path) && ! $public->delete($path)) {
                        $this->components->error("Could not remove public copy of attachment {$attachment->id}.");
                        $failed++;
                        continue;
                    }

                    $attachment->forceFill(['disk' => 'local'])->save();
                    $moved++;
                }
            });

        $this->components->info("Privatized {$moved} project attachment(s); {$failed} need manual review.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
