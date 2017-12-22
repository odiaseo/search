<?php

namespace MapleSyrupGroup\Search\Cache\Adapter;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

/**
 * A replacement for Symfony\Component\Cache\Adapter\FilesystemAdapter which does not group cache files in sub-directories.
 *
 * This class is part of the tests directory as it's only meant to be used with tests.
 *
 * The original cache implementation stores files in sub-directories for performance.
 * Cache generated on our local dev environments (case insensitive) will not work on the CI environment (case sensitive),
 * as the directory name casing will depend on which file was stored first
 * (d123 and Dabc would be stored as d/1/23 and d/a/bc).
 * Since we only use this class for testing with reasonably low amount of files,
 * the problem can be easily solved by storing files in the same directory.
 *
 * Most of the code here is copied over from the original class. Only the getFile() method has been changed.
 */
class FilesystemAdapter extends AbstractAdapter
{
    protected $directory;

    public function __construct($namespace = '', $defaultLifetime = 0, $directory = null)
    {
        parent::__construct('', $defaultLifetime);

        if (!isset($directory[0])) {
            $directory = sys_get_temp_dir() . '/symfony-cache';
        }
        if (isset($namespace[0])) {
            if (preg_match('#[^-+_.A-Za-z0-9]#', $namespace, $match)) {
                throw new InvalidArgumentException(sprintf('FilesystemAdapter namespace contains "%s" but only characters in [-+_.A-Za-z0-9] are allowed.', $match[0]));
            }
            $directory .= '/' . $namespace;
        }
        if (!file_exists($dir = $directory . '/.')) {
            @mkdir($directory, 0777, true);
        }
        if (false === $dir = realpath($dir)) {
            throw new InvalidArgumentException(sprintf('Cache directory does not exist (%s)', $directory));
        }
        if (!is_writable($dir .= DIRECTORY_SEPARATOR)) {
            throw new InvalidArgumentException(sprintf('Cache directory is not writable (%s)', $directory));
        }
        // On Windows the whole path is limited to 258 chars
        if ('\\' === DIRECTORY_SEPARATOR && strlen($dir) > 234) {
            throw new InvalidArgumentException(sprintf('Cache directory too long (%s)', $directory));
        }

        $this->directory = $dir;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        $values = array();
        $now = time();

        foreach ($ids as $id) {
            $file = $this->getFile($id);
            if (!$h = @fopen($file, 'rb')) {
                continue;
            }

            $expiresAt = fgets($h);

            if ($now >= (int) $expiresAt) {
                fclose($h);
                if (isset($expiresAt[0])) {
                    @unlink($file);
                }
            } else {
                $i = rawurldecode(rtrim(fgets($h)));
                $value = stream_get_contents($h);
                fclose($h);
                if ($i === $id) {
                    $values[$id] = unserialize($value);
                }
            }
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchNew(array $ids)
    {
        $values = array();
        $now    = time();

        foreach ($ids as $id) {
            $file = $this->getFile($id);
            if (!file_exists($file)) {
                continue;
            }

            $data      = json_decode(file_get_contents($file));
            $expiresAt = $data->expires;

            if ($now >= (int) $expiresAt) {
                if (isset($expiresAt[0])) {
                    @unlink($file);
                }
            } else {
                $i     = rawurldecode(rtrim($data->id));
                $value = $data->data;
                if ($i === $id) {
                    $values[$id] =  $value;
                }
            }
        }

        return $values;
    }


    /**
     * {@inheritdoc}
     */
    protected function doHave($id)
    {
        $file = $this->getFile($id);

        return file_exists($file) && (@filemtime($file) > time() || $this->doFetch(array($id)));
    }

    /**
     * {@inheritdoc}
     */
    protected function doClear($namespace)
    {
        $ok = true;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->directory, \FilesystemIterator::SKIP_DOTS)) as $file) {
            $ok = ($file->isDir() || @unlink($file) || !file_exists($file)) && $ok;
        }

        return $ok;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete(array $ids)
    {
        $ok = true;

        foreach ($ids as $id) {
            $file = $this->getFile($id);
            $ok   = (!file_exists($file) || @unlink($file) || !file_exists($file)) && $ok;
        }

        return $ok;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave(array $values, $lifetime)
    {
        $ok = true;
        $expiresAt = $lifetime ? time() + $lifetime : PHP_INT_MAX;
        $tmp = $this->directory.uniqid('', true);

        foreach ($values as $id => $value) {
            $file = $this->getFile($id);

            $value = $expiresAt."\n".rawurlencode($id)."\n".serialize($value);
            if (false !== @file_put_contents($tmp, $value)) {
                @touch($tmp, $expiresAt);
                $ok = @rename($tmp, $file) && $ok;
            } else {
                $ok = false;
            }
        }

        return $ok;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    protected function getFile($id)
    {
        return $this->directory . hash('crc32', $id) . '.txt';
    }
}