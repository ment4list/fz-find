<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use SimpleXMLElement;
use stdClass;

use function Termwind\{render};

class FindCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'find {name : The site name to search for}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Find an entry';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $query = $this->argument('name');
        $this->info(sprintf('Searching entries for "%s"', $query) );
        $entries = $this->readFilemanagerEntries();

        // Calculate distance to search
        $entries = $entries->map(function (stdClass $entry) use ($query) {
            $lev = levenshtein($entry->name, $query);
            $entry->lev_distance = $lev;
            // $this->info(sprintf('%s - %f', $entry->name, $lev));
            return $entry;
        });

        $sorted = $entries->sortBy('lev_distance');
        // Take the first x items
        $matches = $sorted->take(2);

        // $matches = $entries->filter(function (stdClass $entry, int $key) use ($query) {
        //     return $entry->lev_distance <= 5;
        // });

        // TODO: Instead of outputting first X matches, show first Y matches with option to select. E.g.,
        // Found 3 matches, please choose which to show:
        // 1) match one
        // 2) some site
        // 3) another site name

        if($matches->count()) {

            $html = "";
            $html .= sprintf('<div class="mb-2 bg-green-300 text-black">Found %d matches</div><br>', $matches->count());
            render($html);

            foreach($matches as $found) {
                $html = "";
                $html .= '<div class="mb-1">';
                // $html .= sprintf('<div class=" bg-blue-300 text-black">Found "%s" with Levenshtein Dist. of %d</div><br>', $found->name, $found->lev_distance);
                    $html .= '<table>';
                        $html .= sprintf('<tr><th>Name</th><td>%s</td>', $found->name);
                        $html .= sprintf('<tr><th>Levenshtein Dist</th><td>%s</td>', $found->lev_distance);
                        $html .= sprintf('<tr><th>Host</th><td>%s</td>', $found->host);
                        $html .= sprintf('<tr><th>Username</th><td>%s</td>', $found->username);
                        $html .= sprintf('<tr><th>Password</th><td>%s</td>', $found->password);
                    $html .= '</table>';
                $html .= '</div>';
                render($html);
            }

        } else {
            $this->warn('Could not find site.');
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }


    public function readFilemanagerEntries() {
        $entries = collect([]);

        // $_SERVER['HOME'] == ~ == /Users/username
        $filePath = sprintf('%s/.config/filezilla/sitemanager.xml', $_SERVER['HOME']);
        if (File::exists(realpath($filePath))) {
            // $this->info( sprintf("\tParsing file: %s", $filePath) );

            $xml_str = file_get_contents($filePath);
            $xml = simplexml_load_string( $xml_str );

            foreach( $xml->xpath( '//Server') as $server) {
                // $attributes = $server->attributes();
                $entry = new \stdClass();

                $entry->lev_distance = 99999;
                $entry->name = (string) $server->Name;
                $entry->host = (string) $server->Host;
                $entry->port = (string) $server->Port;
                $entry->username = (string) $server->User;
                $entry->password = (string) $server->Pass ? base64_decode($server->Pass) : 'NOT_SET';

                $entries->add($entry);
            }
        }

        $this->info( sprintf("\tIndexed %d entries", $entries->count()) );
        return $entries;
    }
}
