<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use AlanVdb\Renderer\PhpRenderer;
use AlanVdb\Renderer\Exception\TemplateNotFoundException;
use AlanVdb\Renderer\Exception\InvalidTemplateVarProvided;

#[CoversClass(PhpRenderer::class)]
class PhpRendererTest extends TestCase
{
    protected PhpRenderer $renderer;
    protected string $templateDir;

    protected function setUp(): void
    {
        $this->renderer = new PhpRenderer();
        $this->templateDir = __DIR__ . '/templates';

        if (!is_dir($this->templateDir)) {
            mkdir($this->templateDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->templateDir);
    }

    #[\PHPUnit\Test]
    public function testRenderThrowsTemplateNotFoundException(): void
    {
        $file = $this->templateDir . '/nonexistent_template.php';
        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessage("Cannot find template file: '$file'.");
        $this->renderer->render($file);
    }

    #[\PHPUnit\Test]
    public function testRenderThrowsInvalidTemplateVarProvided(): void
    {
        $template = $this->createTemplate('sample_template.php', '<p>Hello, <?= $name ?>!</p>');

        $invalidVars = ['1invalid' => 'value']; // Invalid variable name

        $this->expectException(InvalidTemplateVarProvided::class);
        $this->expectExceptionMessage('Invalid $vars array key');

        $this->renderer->render($template, $invalidVars);
    }

    #[\PHPUnit\Test]
    public function testRenderOutputsContent(): void
    {
        $template = $this->createTemplate('sample_template.php', '<p>Hello, <?= $name ?>!</p>');

        $output = $this->renderer->render($template, ['name' => 'John']);

        $this->assertEquals('<p>Hello, John!</p>', $output);
    }

    #[\PHPUnit\Test]
    public function testRenderWithNoVars(): void
    {
        $template = $this->createTemplate('sample_template.php', '<p>No variables passed!</p>');

        $output = $this->renderer->render($template);

        $this->assertEquals('<p>No variables passed!</p>', $output);
    }

    #[\PHPUnit\Test]
    public function testAddNamespaceAndRender(): void
    {
        $this->renderer->addNamespace('admin', $this->templateDir . '/admin');
        mkdir($this->templateDir . '/admin');

        $template = $this->createTemplate('admin/dashboard.php', '<h1>Admin Dashboard</h1>');

        $output = $this->renderer->render('admin.dashboard');

        $this->assertEquals('<h1>Admin Dashboard</h1>', $output);
    }

    #[\PHPUnit\Test]
    #[\PHPUnit\Depends('testAddNamespaceAndRender')]
    public function testRenderWithNamespaceAndVars(): void
    {
        $this->renderer->addNamespace('admin', $this->templateDir . '/admin');
        mkdir($this->templateDir . '/admin');

        $template = $this->createTemplate('admin/dashboard.php', '<h1>Hello, <?= $user ?>!</h1>');

        $output = $this->renderer->render('admin.dashboard', ['user' => 'Admin']);

        $this->assertEquals('<h1>Hello, Admin!</h1>', $output);
    }

    protected function createTemplate(string $filename, string $content): string
    {
        $filePath = $this->templateDir . '/' . $filename;
        $dir = dirname($filePath);
    
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    
        file_put_contents($filePath, $content);
        return $filePath;
    }
    

    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->removeDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($dir);
    }
}
