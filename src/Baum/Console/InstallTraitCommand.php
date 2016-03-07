<?php
namespace Baum\Console;

use Baum\Generators\MigrationGenerator;
use Baum\Generators\ModelTraitGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class InstallTraitCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'baum:install-trait';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Scaffolds a new migration and adds Baum trait to existing model.';

  /**
   * Migration generator instance
   *
   * @var Baum\Generators\MigrationGenerator
   */
  protected $migrator;

  /**
   * Model generator instance
   *
   * @var Baum\Generators\ModelTraitGenerator
   */
  protected $modeler;

  /**
   * Location of class file
   * 
   * @var string
   */
  protected $fileLocation;

  /**
   * Create a new command instance
   *
   * @return void
   */
  public function __construct(MigrationGenerator $migrator, ModelTraitGenerator $modeler) {
    parent::__construct();

    $this->migrator = $migrator;
    $this->modeler  = $modeler;
  }

  /**
   * Execute the console command.
   *
   * Basically, we'll write the migration and model stubs out to disk inflected
   * with the name provided. Once its done, we'll `dump-autoload` for the entire
   * framework to make sure that the new classes are registered by the class
   * loaders.
   *
   * @return void
   */
  public function fire() {
    $name = $this->input->getArgument('name');

    if (! class_exists($name)) {
      $this->error(sprintf("Class %s doesn't exist!", $name));
      return;
    }

    $reflector = new \ReflectionClass($name);
    $fileLocation = $reflector->getFileName();

    if ($fileLocation) {
      $this->fileLocation = $fileLocation;
    } else {
      $this->error(sprintf("Class %s is not writable!", $name));
      return;
    }

    $this->writeMigration($name);

    $this->writeModel($name);

  }

  /**
   * Get the command arguments
   *
   * @return array
   */
  protected function getArguments() {
    return array(
      array('name', InputArgument::REQUIRED, 'Name of the class to be added nested sets to and of the migration and model.')
    );
  }

  /**
   * Write the migration file to disk.
   *
   * @param  string  $name
   * @return string
   */
  protected function writeMigration($name) {
    $output = pathinfo($this->migrator->create($name, $this->getMigrationsPath(), true), PATHINFO_FILENAME);

    $this->line("      <fg=green;options=bold>create</fg=green;options=bold>  $output");
  }

  /**
   * Write the model file to disk.
   *
   * @param  string  $name
   * @return string
   */
  protected function writeModel($name) {
      $output = pathinfo($this->modeler->addTrait($name, $this->fileLocation), PATHINFO_FILENAME);

      $this->line("      <fg=green;options=bold>create</fg=green;options=bold>  $output");
  }

  /**
   * Get the path to the migrations directory.
   *
   * @return string
   */
  protected function getMigrationsPath() {
    return $this->laravel['path.database'].'/migrations';
  }

  /**
   * Get the path to the models directory.
   *
   * @return string
   */
  protected function getModelsPath() {
    return $this->laravel['path.base'];
  }

}
