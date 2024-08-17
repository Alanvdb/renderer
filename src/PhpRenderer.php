<?php declare(strict_types=1);

namespace AlanVdb\Renderer;

use AlanVdb\Renderer\Definition\RendererInterface;
use AlanVdb\Renderer\Exception\TemplateNotFoundException;
use AlanVdb\Renderer\Exception\InvalidTemplateVarProvided;

class PhpRenderer implements RendererInterface
{
    private array $namespaces = [];

    /**
     * Add a namespace for templates.
     *
     * @param string $namespace The namespace to add.
     * @param string $path The path associated with the namespace.
     * @return void
     */
    public function addNamespace(string $namespace, string $path): void
    {
        $this->namespaces[$namespace] = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * Render a template with the provided variables.
     *
     * @param string $template The name of the template to render.
     * @param array $vars The variables to extract into the template context.
     * @return string The rendered content.
     * @throws TemplateNotFoundException If the template is not found.
     * @throws InvalidTemplateVarProvided If an invalid variable name is provided.
     */
    public function render(string $template, array $vars = []) : string
    {
        $templatePath = $this->findTemplate($template);

        if ($templatePath === null) {
            throw new TemplateNotFoundException("Cannot find template file: '$template'.");
        }

        foreach (array_keys($vars) as $varName) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $varName)) {
                throw new InvalidTemplateVarProvided(
                    'Invalid $vars array key: "' . $varName . '" cannot be extracted as PHP var name.'
                );
            }
        }

        ob_start();
        extract($vars);
        require $templatePath;
        return ob_get_clean();
    }

    /**
     * Find the template path using namespaces.
     *
     * @param string $template The name of the template to search for.
     * @return string|null The full path to the template or null if not found.
     */
    private function findTemplate(string $template): ?string
    {
        // If the template is a direct file path
        if (is_file($template)) {
            return $template;
        }

        // If the template contains a namespace separator (.)
        if (strpos($template, '.') !== false) {
            list($namespace, $template) = explode('.', $template, 2);
            if (isset($this->namespaces[$namespace])) {
                // Convert the remaining part of the template path to the correct directory structure
                $templatePath = $this->namespaces[$namespace] . str_replace('.', DIRECTORY_SEPARATOR, $template) . '.php';
                if (is_file($templatePath)) {
                    return $templatePath;
                }
            }
        } else {
            // No namespace, search in all registered namespace paths
            foreach ($this->namespaces as $path) {
                $templatePath = $path . ltrim($template, '/\\');
                if (is_file($templatePath)) {
                    return $templatePath;
                }
            }
        }

        return null;
    }
}
