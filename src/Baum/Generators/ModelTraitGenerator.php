<?php
namespace Baum\Generators;

class ModelTraitGenerator extends Generator {

  /**
   * Add trait to class at a given path.
   *
   * @param  string  $name
   * @param  string  $path
   * @return string
   */
  public function addTrait($name, $path) {
    $source = $this->files->get($path);

    $pos = strpos($source, '{') + 1;

    $newSource = substr_replace($source, "\n    use \Baum\NodeTrait;\n", $pos, 0);

    $this->files->put($path, $newSource);

    return $path;
  }
}
