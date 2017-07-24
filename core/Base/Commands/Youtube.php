<?php

namespace Core\Base\Commands;

use Core\Command\Command;
use Core\Command\Parameters;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use YoutubeDl\YoutubeDl;

class Youtube extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'yt';

    /**
     * {@inheritdoc}
     */
    protected $description = '';

    /**
     * Default command method.
     *
     * @param \Core\Commands\Parameters $p
     *
     * @return void
     */
    public function index(Parameters $p)
    {
        // Default command method
        // $dl = new YoutubeDl([
        //     'extract-audio' => true,
        //     'audio-format' => 'mp3',
        //     'audio-quality' => 0, // best
        //     'output' => '%(title)s.%(ext)s',
        // ]);

        // $dl->setDownloadPath(tmp_path());

        // try {
        //     $video = $dl->download($p->first());
        //     echo $video->getTitle();
        // } catch (\Exception $ex) {
        //     dump($ex->getMessage());
        // }

        dump($this->author);
    }

    // Place your methods here
}
