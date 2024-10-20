<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadBookPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $book;

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(): BinaryFileResponse
    {
        $fileUrl = $this->book->getFirstMediaPath('file');

        if ($fileUrl && file_exists($fileUrl)) {
            // Increment real and fake downloads count
            $this->book->increment('real_downloads_count');
            $this->book->increment('fake_downloads_count');

            // Return the file as a downloadable response
            return response()->download($fileUrl, "{$this->book->title}.pdf");
        }

        // Handle case where file doesn't exist
        throw new \Exception('File not found.');
    }
}
