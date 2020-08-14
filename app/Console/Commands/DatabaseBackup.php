<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creare database backup';

    protected $process;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        
        // dd(sprintf(
        //     'mysqldump -u%s -p%s %s > %s',
        //     config('database.connections.mysql.username'),
        //     config('database.connections.mysql.password'),
        //     config('database.connections.mysql.database'),
        //     storage_path('backups/backup_' . date('Y_m_d_H_i_s') . '.sql')));

        $this->process = new Process([
            'mysqldump -u' . config('database.connections.mysql.username') . ' -p' . config('database.connections.mysql.password') . ' ' .  config('database.connections.mysql.database') . ' > ' . storage_path('backups\backup.sql')
        ]);
    }

    public function handle()
    {
        try 
        {
            $this->process->mustRun();
            $this->info('The backup has been proceed successfully.');
        } 
        catch (ProcessFailedException $exception) 
        {
            dd($exception);
            $this->error('The backup process has been failed.');
        }
    }
}
