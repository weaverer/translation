<?php
declare(strict_types=1);

namespace Weaverer\Translation;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator as LaravelTranslator;

class Translator implements TranslatorContract
{
    private static array $instance;
    private LaravelTranslator $translator;

    private function __construct($path)
    {
       $file= new Filesystem();
       $fileLoader = new FileLoader($file, $path);
       $this->translator = new LaravelTranslator($fileLoader, 'en');
    }

    public static function getInstance(string $dir = null): Translator
    {
        $dir = $dir ?? __DIR__.'/../vendor/illuminate/translation/lang';
        $dirHash = md5($dir);
        if (null === self::$instance[$dir]) {
            self::$instance[$dirHash] = new self($dir);
        }
        return self::$instance[$dirHash];
    }

    public function get($key, array $replace = [], string $locale = null): string
    {
        return $this->translator->get($key, $replace, $locale);
    }

    public function choice($key, $number, array $replace = [], $locale = null): string
    {
        return $this->translator->choice($key, $number, $replace, $locale);
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    public function setLocale($locale): void
    {
        $this->translator->setLocale($locale);
    }


    public function __clone()
    {
        throw new \Exception('Cloning ' . __CLASS__ . ' is not allowed.');
    }

    public function __wakeup()
    {
        throw new \Exception('Serializing ' . __CLASS__ . ' is not allowed.');
    }
}
