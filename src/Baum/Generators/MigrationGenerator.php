<?php
namespace Baum\Generators;

class MigrationGenerator extends Generator {

  /**
   * Create a new migration at the given path.
   *
   * @param  string  $name
   * @param  string  $path
   * @param  boolean $isTrait
   * @return string
   */
  public function create($name, $path, $isTrait = false) {

    if ($isTrait) {
      $stub = $this->getStub('migration-trait');
      $name = basename(str_replace('\\','/', $name));
    } else {
      $stub = $this->getStub('migration');
    }

    $path = $this->getPath($name, $path, $isTrait);

    $this->files->put($path, $this->parseStub($stub, array(
      'table' => $this->tableize($name),
      'class' => $this->getMigrationClassName($name, $isTrait)
    )));

    return $path;
  }

  /**
   * Get the migration name.
   *
   * @param string $name
   * @param boolean $isTrait
   * @return string
   */
  protected function getMigrationName($name, $isTrait) {
    if ($isTrait) {
      return 'add_nested_sets_to_' . $this->tableize($name) . '_table';
    } else {
      return 'create_' . $this->tableize($name) . '_table';
    }
  }

  /**
   * Get the name for the migration class.
   *
   * @param string $name
   */
  protected function getMigrationClassName($name, $isTrait) {
    return $this->classify($this->getMigrationName($name, $isTrait));
  }

  /**
   * Get the full path name to the migration.
   *
   * @param  string  $name
   * @param  string  $path
   * @param boolean $isTrait
   * @return string
   */
  protected function getPath($name, $path, $isTrait) {
    return $path . '/' . $this->getDatePrefix() . '_' . $this->getMigrationName($name, $isTrait) . '.php';
  }

  /**
   * Get the date prefix for the migration.
   *
   * @return int
   */
  protected function getDatePrefix() {
    return date('Y_m_d_His');
  }

}
