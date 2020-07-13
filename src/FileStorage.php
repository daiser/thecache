<?php


namespace MaximalTestWork;


use MaximalTestWork\Contracts\StorageInterface;

/*
 * Самый простой способ - SQLite. Технически это "в файлы" и не требует настройки. Но типа хак.
 *
 * Второй простой способ - JSON. Наследуемся от MemoryStorage и при каждом изменении сохраняем
 * содержимое MemoryStorage::$container в файл. При чтении из Хранилища читаем и парсим JSON
 * в $container. Не самая быстрая штука получится.
 *
 * Поэтому.
 *
 * Один файл - одно значение. Но если ключ, значение и время жизни хратить в самом файле, а сами файлы,
 * например, нумеровать последовательно, то нужно читать и парсить все файлы, чтобы найти данные
 * по ключу.
 *
 * Поэтому именем файла будет хэш ключа. Например, MD5. Возможны коллизии, но ой. Скрестим пальцы
 * на удачу. Также в имя файла добавим время жизни значения. Таким образом в файле останется
 * только само значение.
 */

class FileStorage implements StorageInterface {
    private const RX_EXPIRING_FILENAME_HASH = '/^(?<keyhash>[0-9a-f]{32})_(?<et>\d+)\.value$/';
    private const RX_FILENAME               = '/^(?<keyhash>[0-9a-f]{32})_\.value$/';
    private $path;



    function __construct(string $storagePath = null) {
        $this->path = $storagePath ?? sys_get_temp_dir() ?? '.';
    }



    private static function hashKey(string $key): string {
        return md5($key);
    }



    function containsKey(string $key): bool {
        try {
            $this->findContainer($key);

            return true;
        }
        catch (NotFoundException $ignored) {
            return false;
        }
    }



    function create(string $key, string $value) {
        file_put_contents($this->makeFileName($key), $value);
    }



    function createUntil(string $key, string $value, int $expiresAt) {
        file_put_contents($this->makeExpiringFileName($key, $expiresAt), $value);
    }



    /**
     * @param string $key
     *
     * @return string
     * @throws NotFoundException
     */
    private function findContainer(string $key): string {
        $keyHash   = self::hashKey($key);
        $fullPaths = glob($this->path . "/{$keyHash}_*.value");
        if (empty($fullPaths))
            throw new NotFoundException("Container for key '{$key}' not found");
        if (count($fullPaths) > 1)
            throw new InvalidOperationException("Multiple containers for key '{$key}' found");

        return basename($fullPaths[0]);
    }



    function isValid(string $key): bool {
        $now       = time();
        $container = $this->findContainer($key);

        if (preg_match_all(self::RX_FILENAME, $container) === 1)
            return true;
        if (preg_match_all(self::RX_EXPIRING_FILENAME_HASH, $container, $matches) === 1) {
            $expiresAt = intval($matches['et'][0]);

            return $expiresAt > $now;
        }

        return false;
    }



    private function makeExpiringFileName(string $key, int $expiresAt): string {
        return $this->makeFullName(sprintf("%s_%d.value", self::hashKey($key), $expiresAt));
    }



    private function makeFileName(string $key): string {
        return $this->makeFullName(self::hashKey($key) . '_.value');
    }



    private function makeFullName(string $filename): string {
        return $this->path . '/' . $filename;
    }



    function read(string $key): string {
        if (!$this->containsKey($key))
            throw new NotFoundException("Key '{$key}' not found");
        if (!$this->isValid($key))
            throw new ValueExpiredException("Value for key '{$key}' is expired");

        return file_get_contents($this->makeFullName($this->findContainer($key)));
    }



    function remove(string $key) {
        try {
            unlink($this->makeFullName($this->findContainer($key)));
        }
        catch (NotFoundException $ignored) {
            // do nothing
        }
    }
}
