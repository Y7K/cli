<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 17.03.18
 * Time: 14:21
 */

namespace App\Commands;


use App\Concerns\HasProcess;
use App\Concerns\InstallsRepository;
use App\Helpers\FileHelper;

class Craft2UpdateCommand extends BaseCommand
{

    use HasProcess, InstallsRepository;

    protected $signature = 'craft2:update ' .
    '{--c|commit : Commit update to git directly?}';
    protected $description = '🔃  Update Craft 2 to the latest version.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        $craftAppDir = $this->getWorkingDirectory() . '/craft/app';

        $this->info('Deleting /craft/app folder...');

        FileHelper::deleteDirectory($craftAppDir);

        $this->info('Download the lastest Version of Craft...');

        $this->installRepositoryFromUrl('http://craftcms.com/latest.zip?accept_license=yes', [
            'destinationPath' => $craftAppDir,
            'subfolders' => ['craft/app']
        ]);

        $this->info('Craft App folder installed!');

        require_once ($craftAppDir . '/info.php');

        if($this->option('commit')) {

            $this->info('Commit update to git...');

            $this->runProcessSequence([
                "git add craft/app",
                "git commit -m \"Craft Updated to Version " . CRAFT_VERSION ."\""
            ]);
        }

        $this->info('Craft updated to Version ' . CRAFT_VERSION);
    }

}
